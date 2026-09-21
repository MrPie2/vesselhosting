<?php namespace App\Services; 
use Exception; 
use Illuminate\Support\Facades\Http; 

class ResellerClubService 
{ 
    protected string $url; 
    protected string $userId; 
    protected string $apiKey;

     public function __construct() { 
        $this->url = rtrim( (string) config('services.resellerclub.url'), '/' ); 
        $this->userId = (string) config( 'services.resellerclub.user_id' ); 
        $this->apiKey = (string) config( 'services.resellerclub.api_key' ); 

        if (empty($this->url)) { 
            throw new Exception( 'ResellerClub API URL is not configured.' ); } 
            if (empty($this->userId)) { 
                throw new Exception( 'ResellerClub User ID is not configured.' ); 
                } 
                
                if (empty($this->apiKey)) { 
                    throw new Exception( 'ResellerClub API key is not configured.' ); } 
                    } 
                    public function check(string $domain): array { 
                        try { $domain = strtolower(trim($domain)); 
                        // Remove http:// or https:// 
                        $domain = preg_replace( '#^https?://#i', '', $domain ); 
                        // // Remove paths 
                        $domain = explode('/', $domain)[0]; // Remove query strings 
                        $domain = explode('?', $domain)[0]; // Remove trailing dot 
                        $domain = rtrim($domain, '.'); 
                        if (empty($domain)) { 
                            return [ 'success' => false, 'message' => 'Domain name is required.', ]; } 
                            
                            $parts = explode('.', $domain); 
                            
                            if (count($parts) < 2) { 
                                return [ 'success' => false, 'message' => 'Invalid domain name.', ]; 
                                } 
                                $domainName = $parts[0]; 
                                $tld = implode( '.', array_slice($parts, 1) ); 
                                if (empty($domainName) || empty($tld)) { 
                                    return [ 'success' => false, 'message' => 'Invalid domain name.', ]; 
                                    } 
                                    $response = Http::timeout(15) ->acceptJson() ->withHeaders([ 
                                        'x-user-id' => $this->userId, 
                                        'Authorization' => 'ApiKey ' . $this->apiKey, ]) ->get( $this->url . '/domains/available', [ 'domainName' => $domainName, 'tlds' => $tld, ] ); if ($response->failed()) { return [ 'success' => false, 'message' => 'ResellerClub API request failed.', 'status' => $response->status(), 'domain' => $domain, 'response' => $response->json(), ]; } $data = $response->json(); 
                                        return [ 'success' => true, 'domain' => $domain, 'available' => (bool) ( $data['available'] ?? false ), 'data' => $data, ]; } 
                                        catch (Exception $e) { 
                                            return [ 'success' => false, 'message' => $e->getMessage(), ]; 
                                        } 
                                         } 
                                 }