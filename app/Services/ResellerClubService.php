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

        if (!$domain) {
            return ['success' => false, 'message' => 'Domain name is required.'];
        }

        if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\\.)+[a-z]{2,63}$/i', $domain)) {
            return ['success' => false, 'message' => 'Enter a valid domain name.'];
        }

        [$domainName, $tld] = $this->splitDomain($domain);

        if ($this->userId === '' || $this->apiKey === '') {
            return [
                'success' => false,
                'message' => 'ResellerClub API credentials are not configured on the server.',
                'domain' => $domain,
            ];
        }

        if ($this->legacyUrl === '') {
            return [
                'success' => false,
                'message' => 'ResellerClub API URL is not configured on the server.',
                'domain' => $domain,
            ];
        }

        try {
            /*
             * ResellerClub's HTTP API uses auth-userid/api-key query
             * parameters. The availability endpoint returns a map where
             * the requested domain is keyed to a status such as:
             * available, regthroughus, regthroughothers or unknown.
             */
            // Laravel/Guzzle serializes PHP arrays as domain-name[0]=...
            // but the legacy ResellerClub API expects repeated query keys:
            // domain-name=example.com&domain-name=example.net&tlds=com&tlds=net.
            // Build the query explicitly so the provider receives the exact
            // parameter names documented by ResellerClub.
            $query = http_build_query([
                'auth-userid' => $this->userId,
                'api-key' => $this->apiKey,
            ]);

            $query .= '&domain-name=' . rawurlencode($domainName);
            $query .= '&tlds=' . rawurlencode($tld);

            $response = Http::timeout(20)->acceptJson()->get(
                $this->legacyUrl . '/domains/available.json?' . $query
            );

            if ($response->failed()) {
                $body = trim($response->body());

                report(new \RuntimeException(
                    'ResellerClub availability request failed: HTTP ' .
                    $response->status() . ($body ? ' - ' . $body : '')
                ));

                return [
                    'success' => false,
                    'message' => $this->providerErrorMessage($response->status(), $body),
                    'domain' => $domain,
                    'provider_http_status' => $response->status(),
                    'provider_response' => $this->safeProviderResponse($body),
                ];
            }

            $data = $response->json();

            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'ResellerClub returned an invalid availability response.',
                    'domain' => $domain,
                ];
            }

            // ResellerClub can return API errors as JSON with HTTP 500.
            // Surface the provider's safe error message instead of hiding it
            // behind a generic availability failure.
            if (isset($data['status']) && strtoupper((string) $data['status']) === 'ERROR') {
                $providerMessage = $data['message'] ?? $data['error'] ?? null;

                return [
                    'success' => false,
                    'message' => $providerMessage
                        ? 'ResellerClub API error: ' . (string) $providerMessage
                        : 'ResellerClub rejected the API request. Check the reseller ID, API key and server IP whitelist.',
                    'domain' => $domain,
                    'provider_http_status' => $response->status(),
                    'provider_status' => $data['status'],
                    'provider_response' => $data,
                ];
            }

            $status = $data[$domain] ?? null;

            // The legacy API normally keys the response by the full domain,
            // but the request's domain-name value is the SLD. Accept both
            // shapes because the provider may return either.

            /*
             * Some responses use the domain label as the key because
             * domain-name and tlds are submitted separately.
             */
            if ($status === null) {
                $status = $data[$domainName] ?? null;
            }

            /*
             * Some responses can use a normalized/case-different key.
             */
            if ($status === null) {
                foreach ($data as $key => $value) {
                    if (strcasecmp((string) $key, $domain) === 0 || strcasecmp((string) $key, $domainName) === 0) {
                        $status = $value;
                        break;
                    }
                }
            }

            if (is_array($status)) {
                $status = $status['status'] ?? $status['availability'] ?? null;
            }

            if (is_string($status)) {
                $status = strtolower(trim($status));
            }

            // A successful legacy response is a hash map of domain => status.
            // If the provider returns exactly one entry, use that value as
            // a safe fallback instead of treating the response as unexpected.
            if ($status === null && count($data) === 1) {
                $onlyValue = reset($data);
                if (is_string($onlyValue)) {
                    $status = strtolower(trim($onlyValue));
                }
            }

            if ($status === 'available') {
                return [
                    'success' => true,
                    'domain' => $domain,
                    'available' => true,
                    'status' => 'available',
                    'data' => $data,
                ];
            }

            if (in_array($status, ['regthroughus', 'regthroughothers', 'unknown'], true)) {
                return [
                    'success' => true,
                    'domain' => $domain,
                    'available' => false,
                    'status' => $status,
                    'data' => $data,
                    'message' => $status === 'unknown'
                        ? 'ResellerClub could not determine the domain status. Please try again.'
                        : $domain . ' is already registered.',
                ];
            }

            return [
                'success' => false,
                'message' => 'ResellerClub returned an unexpected availability response.',
                'domain' => $domain,
                'provider_http_status' => $response->status(),
                'provider_response' => $data,
            ];
        } catch (Throwable $e) {
            report($e);

            return [
                'success' => false,
                'message' => 'Unable to connect to ResellerClub. Please try again shortly.',
                'domain' => $domain,
                'provider_error' => $e->getMessage(),
            ];
        }
    }

    private function providerErrorMessage(int $httpStatus, string $body): string
    {
        $decoded = json_decode($body, true);

        if (is_array($decoded)) {
            $providerMessage = $decoded['message']
                ?? $decoded['error']
                ?? $decoded['description']
                ?? null;

            if (is_string($providerMessage) && trim($providerMessage) !== '') {
                return 'ResellerClub API error: ' . trim($providerMessage);
            }
        }

        if ($body !== '') {
            return 'ResellerClub API error (HTTP ' . $httpStatus . '): ' . $this->truncateProviderBody($body);
        }

        return 'ResellerClub rejected the availability request (HTTP ' . $httpStatus . ').';
    }

    private function safeProviderResponse(string $body): mixed
    {
        $decoded = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
            return $decoded;
        }

        return $body !== '' ? $this->truncateProviderBody($body) : null;
    }

    private function truncateProviderBody(string $body, int $limit = 2000): string
    {
        $body = trim($body);

        return strlen($body) > $limit
            ? substr($body, 0, $limit) . '…'
            : $body;
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
