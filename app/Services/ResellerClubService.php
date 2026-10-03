<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class ResellerClubService
{
    protected string $url;
    protected string $legacyUrl;
    protected string $userId;
    protected string $apiKey;

    public function __construct()
    {
        $this->url = rtrim((string) config('services.resellerclub.url'), '/');
        $this->legacyUrl = rtrim((string) config('services.resellerclub.legacy_url', 'https://domaincheck.httpapi.com/api'), '/');
        $this->userId = (string) config('services.resellerclub.user_id');
        $this->apiKey = (string) config('services.resellerclub.api_key');
    }

    public function check(string $domain): array
    {
        $domain = $this->normaliseDomain($domain);

        if (!$domain) return ['success' => false, 'message' => 'Domain name is required.'];

        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', $domain)) {
            return ['success' => false, 'message' => 'Enter a valid domain name.'];
        }

        [$domainName, $tld] = $this->splitDomain($domain);

        try {
            $response = Http::timeout(15)->acceptJson()->withHeaders([
                'x-user-id' => $this->userId,
                'Authorization' => 'ApiKey ' . $this->apiKey,
            ])->get($this->url . '/domains/available', [
                'domainName' => $domainName,
                'tlds' => $tld,
            ]);

            if ($response->failed()) {
                return ['success' => false, 'message' => 'ResellerClub availability check failed.', 'domain' => $domain];
            }

            $data = $response->json();

            return [
                'success' => true,
                'domain' => $domain,
                'available' => (bool) ($data['available'] ?? false),
                'data' => $data,
            ];
        } catch (Throwable $e) {
            report($e);
            return ['success' => false, 'message' => 'Domain availability is temporarily unavailable.', 'domain' => $domain];
        }
    }

    public function suggestions(string $domain, int $limit = 10): array
    {
        $domain = $this->normaliseDomain($domain);
        if (!$domain) return [];

        [$keyword, $tld] = $this->splitDomain($domain);
        $keyword = preg_replace('/[-]+/', ' ', $keyword);

        try {
            $response = Http::timeout(15)->acceptJson()->get($this->legacyUrl . '/domains/v5/suggest-names.json', [
                'auth-userid' => $this->userId,
                'api-key' => $this->apiKey,
                'keyword' => $keyword,
                'tld-only' => $tld,
                'exact-match' => 'false',
                'adult' => 'false',
            ]);

            if ($response->failed()) return [];

            $payload = $response->json();
            $suggestions = [];

            $collect = function ($value) use (&$collect, &$suggestions, $limit): void {
                if (count($suggestions) >= $limit || !is_array($value)) return;

                foreach ($value as $key => $item) {
                    if (is_string($key) && str_contains($key, '.')) $suggestions[] = strtolower($key);
                    if (is_string($item) && str_contains($item, '.')) $suggestions[] = strtolower($item);
                    if (is_array($item)) $collect($item);
                    if (count($suggestions) >= $limit) break;
                }
            };

            $collect($payload);

            return array_values(array_unique(array_slice($suggestions, 0, $limit)));
        } catch (Throwable $e) {
            report($e);
            return [];
        }
    }

    private function normaliseDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = explode('/', $domain)[0];
        $domain = explode('?', $domain)[0];
        return rtrim($domain, '.');
    }

    private function splitDomain(string $domain): array
    {
        $parts = explode('.', $domain);
        return [array_shift($parts), implode('.', $parts)];
    }
}
