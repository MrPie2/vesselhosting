<?php

namespace App\Services;

use App\Models\DnsRecord;
use App\Models\Domain;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class ResellerClubDnsService
{
    private string $baseUrl;
    private string $userId;
    private string $apiKey;
    private int $ttl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.resellerclub.dns_url', 'https://httpapi.com/api'),
            '/'
        );
        $this->userId = (string) config('services.resellerclub.user_id');
        $this->apiKey = (string) config('services.resellerclub.api_key');
        $this->ttl = (int) config('services.resellerclub.dns_ttl', 14400);
    }

    public function add(Domain $domain, DnsRecord $record): void
    {
        $type = strtoupper($record->type);

        if (in_array($type, ['CAA', 'SVCB', 'HTTP'], true)) {
            throw new RuntimeException(
                "{$type} records are not exposed by ResellerClub's documented legacy DNS HTTP API."
            );
        }

        if (!in_array($type, ['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'DMARC', 'SRV'], true)) {
            throw new RuntimeException("Unsupported DNS record type: {$type}");
        }

        $host = $this->host($record->name, $domain->domain);
        $value = trim($record->content);

        if ($type === 'DMARC') {
            $type = 'TXT';
            $host = '_dmarc';
        }

        $path = match ($type) {
            'A' => '/dns/manage/add-ipv4-record.json',
            'AAAA' => '/dns/manage/add-ipv6-record.json',
            'CNAME' => '/dns/manage/add-cname-record.json',
            'MX' => '/dns/manage/add-mx-record.json',
            'TXT' => '/dns/manage/add-txt-record.json',
            'SRV' => '/dns/manage/add-srv-record.json',
        };

        $payload = [
            'domain-name' => $domain->domain,
            'host' => $host,
            'value' => $value,
            'ttl' => $this->ttl,
        ];

        if ($type === 'MX') {
            $payload['priority'] = $record->priority ?? 10;
        }

        if ($type === 'SRV') {
            [$target, $port, $weight] = $this->parseSrvValue($value);
            $payload['value'] = $target;
            $payload['port'] = $port;
            $payload['weight'] = $weight;
            $payload['priority'] = $record->priority ?? 0;
        }

        $this->post($path, $payload);
    }

    public function delete(Domain $domain, DnsRecord $record): void
    {
        $type = strtoupper($record->type);

        if (in_array($type, ['CAA', 'SVCB', 'HTTP'], true)) {
            throw new RuntimeException(
                "{$type} records are not exposed by ResellerClub's documented legacy DNS HTTP API."
            );
        }

        $providerType = $type === 'DMARC' ? 'TXT' : $type;
        $host = $type === 'DMARC' ? '_dmarc' : $this->host($record->name, $domain->domain);
        $value = trim($record->content);

        $path = match ($providerType) {
            'A' => '/dns/manage/delete-ipv4-record.json',
            'AAAA' => '/dns/manage/delete-ipv6-record.json',
            'CNAME' => '/dns/manage/delete-cname-record.json',
            'MX' => '/dns/manage/delete-mx-record.json',
            'TXT' => '/dns/manage/delete-txt-record.json',
            'SRV' => '/dns/manage/delete-srv-record.json',
            default => throw new RuntimeException("Unsupported DNS record type: {$type}"),
        };

        $payload = [
            'domain-name' => $domain->domain,
            'host' => $host,
            'value' => $value,
        ];

        if ($providerType === 'MX' || $providerType === 'SRV') {
            $payload['priority'] = $record->priority ?? 0;
        }

        if ($providerType === 'SRV') {
            [, $port, $weight] = $this->parseSrvValue($value);
            $payload['port'] = $port;
            $payload['weight'] = $weight;
        }

        $this->post($path, $payload);
    }

    private function post(string $path, array $payload): array
    {
        if ($this->userId === '' || $this->apiKey === '') {
            throw new RuntimeException('ResellerClub API credentials are not configured.');
        }

        try {
            $response = Http::timeout(20)
                ->asForm()
                ->acceptJson()
                ->post($this->baseUrl . $path, array_merge([
                    'auth-userid' => $this->userId,
                    'api-key' => $this->apiKey,
                ], $payload));

            if ($response->failed()) {
                throw new RuntimeException(
                    'ResellerClub DNS request failed (HTTP ' . $response->status() . '): ' .
                    $this->providerMessage($response->json(), $response->body())
                );
            }

            $data = $response->json();

            if (!is_array($data)) {
                throw new RuntimeException('ResellerClub returned an invalid DNS response.');
            }

            $status = strtolower((string) ($data['status'] ?? ''));

            if ($status === 'error' || isset($data['error'])) {
                throw new RuntimeException(
                    $this->providerMessage($data, $response->body())
                );
            }

            if ($status !== '' && !in_array($status, ['success', 'ok'], true)) {
                throw new RuntimeException(
                    $this->providerMessage($data, $response->body())
                );
            }

            return $data;
        } catch (Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }

            report($e);
            throw new RuntimeException('Unable to connect to ResellerClub DNS API.', 0, $e);
        }
    }

    private function providerMessage(?array $data, string $body): string
    {
        if (is_array($data)) {
            foreach (['message', 'error', 'description'] as $key) {
                if (isset($data[$key]) && is_string($data[$key]) && trim($data[$key]) !== '') {
                    return trim($data[$key]);
                }
            }
        }

        return trim($body) !== '' ? mb_substr(trim($body), 0, 500) : 'Unknown provider error.';
    }

    private function host(string $name, string $domain): string
    {
        $name = trim($name);

        if ($name === '' || $name === '@' || $name === $domain) {
            return '@';
        }

        $suffix = '.' . strtolower($domain);
        if (str_ends_with(strtolower($name), $suffix)) {
            return substr($name, 0, -strlen($suffix));
        }

        return rtrim($name, '.');
    }

    private function parseSrvValue(string $value): array
    {
        $parts = array_map('trim', explode(':', $value));

        if (count($parts) !== 3) {
            throw new RuntimeException(
                'SRV value must be entered as target:port:weight, for example sip.example.com:5060:10.'
            );
        }

        [$target, $port, $weight] = $parts;

        if ($target === '' || !ctype_digit($port) || !ctype_digit($weight)) {
            throw new RuntimeException(
                'SRV value must be entered as target:port:weight.'
            );
        }

        $port = (int) $port;
        $weight = (int) $weight;

        if ($port < 0 || $port > 65535 || $weight < 0 || $weight > 65535) {
            throw new RuntimeException('SRV port and weight must be between 0 and 65535.');
        }

        return [$target, $port, $weight];
    }
}
