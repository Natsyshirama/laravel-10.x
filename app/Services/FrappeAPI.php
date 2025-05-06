<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class FrappeAPI
{
    protected string $base;

    public function __construct()
    {
        $this->base = rtrim(env('FRAPPE_URL', 'http://erpnext.localhost:8000'), '/');
    }

    
    public function post(string $method, array $payload): array
    {
        $url = "{$this->base}/{$method}";
        $response = Http::post($url, $payload);

        Log::debug("Réponse brute ERPNext (POST {$method}) : " . $response->body());
        Log::debug("Content-Type : " . $response->header('Content-Type'));

        return $response->json();
    }

    
    public function get(string $method, array $query = []): array
{
    $url = "{$this->base}/{$method}";
    $sid = Session::get('sid');

    $response = Http::withHeaders([
        'Cookie' => "sid={$sid}",
    ])->get($url, $query);

    if ($response->successful()) {
        return $response->json() ?? []; // au cas ou json() retourne null
    }

    logger()->error("Erreur HTTP lors de l'appel à Frappe : " . $response->status());
    logger()->error("Réponse : " . $response->body());

    return [];
}

}
