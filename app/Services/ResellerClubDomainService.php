<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class ResellerClubDomainService
{
    public function register(Domain $domain, int $years = 1, ?array $customNameservers = null): array
    {
        $userId = (string) config('services.resellerclub.user_id');
        $apiKey = (string) config('services.resellerclub.api_key');
        $customerId = (string) config('services.resellerclub.customer_id');
        $contactId = (string) config('services.resellerclub.contact_id');
        $url = rtrim((string) (config('services.resellerclub.url') ?: 'https://httpapi.com/api'), '/');

        if ($userId === '' || $apiKey === '' || $customerId === '' || $contactId === '') {
            throw new RuntimeException('ResellerClub domain registration is not configured. Set RESELLERCLUB_CUSTOMER_ID and RESELLERCLUB_CONTACT_ID.');
        }

        $nameservers = $customNameservers !== null
            ? array_values(array_filter(array_map('trim', $customNameservers), fn ($ns) => is_string($ns) && $ns !== ''))
            : config('services.resellerclub.nameservers', []);
        $nameservers = is_array($nameservers)
            ? array_values(array_filter($nameservers, fn ($ns) => is_string($ns) && trim($ns) !== ''))
            : [];

        $query = [
            'auth-userid' => $userId,
            'api-key' => $apiKey,
            'domain-name' => strtolower(trim($domain->domain)),
            'customer-id' => $customerId,
            'reg-contact-id' => $contactId,
            'admin-contact-id' => $contactId,
            'tech-contact-id' => $contactId,
            'billing-contact-id' => $contactId,
            'invoice-option' => 'NoInvoice',
            'years' => max(1, $years),
        ];

        if (empty($nameservers)) {
            throw new RuntimeException('No nameservers are configured for domain registration. Set RESELLERCLUB_NAMESERVERS.');
        }

        $queryString = http_build_query($query);
        foreach ($nameservers as $nameserver) {
            $queryString .= '&ns=' . rawurlencode(trim($nameserver));
        }

        try {
            $response = Http::timeout(30)->acceptJson()->post($url . '/domains/register.json?' . $queryString);
            $data = $response->json();

            if ($response->failed()) {
                throw new RuntimeException($this->providerMessage($response->status(), $response->body()));
            }

            if (is_array($data) && strtoupper((string) ($data['status'] ?? '')) === 'ERROR') {
                throw new RuntimeException('ResellerClub API error: ' . ($data['message'] ?? $data['error'] ?? 'Domain registration was rejected.'));
            }

            if (!is_array($data) && !is_string($data)) {
                throw new RuntimeException('ResellerClub returned an invalid domain registration response.');
            }

            $domain->forceFill([
                'status' => 'active',
                'expiry_date' => now()->addYears(max(1, $years)),
            ])->save();

            return ['success' => true, 'response' => $data, 'nameservers' => $nameservers];
        } catch (Throwable $e) {
            report($e);
            throw $e instanceof RuntimeException
                ? $e
                : new RuntimeException('Unable to register domain with ResellerClub: ' . $e->getMessage(), 0, $e);
        }
    }

    private function providerMessage(int $status, string $body): string
    {
        $data = json_decode($body, true);
        if (is_array($data)) {
            $message = $data['message'] ?? $data['error'] ?? $data['description'] ?? null;
            if (is_string($message) && trim($message) !== '') return 'ResellerClub API error: ' . trim($message);
        }

        return $body !== ''
            ? 'ResellerClub API error (HTTP ' . $status . '): ' . substr(trim($body), 0, 2000)
            : 'ResellerClub rejected the domain registration (HTTP ' . $status . ').';
    }
}
