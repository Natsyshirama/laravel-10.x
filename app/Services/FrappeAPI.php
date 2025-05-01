<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;        
use Illuminate\Support\Facades\Config;  

class FrappeAPI
{
    protected $base;

    public function __construct()
    {
        // Utilise config() plutôt que env()
        $this->base = rtrim(config('frappe.api_base'), '/');
    }
    public function post(string $method, array $payload)
    {
        $url = "{$this->base}/{$method}";
        $response = Http::post($url, $payload);
    
        logger()->debug("Réponse brute ERPNext : " . $response->body());
        logger()->debug("Content-Type : " . $response->header('Content-Type'));
    
        return $response->json();
    }
    

    public function get(string $method, array $query = [])
    {
        $url = "{$this->base}/{$method}";
        return Http::get($url, $query)->json();
    }
}
