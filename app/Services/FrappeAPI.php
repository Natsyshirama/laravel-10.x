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
        // Utilise config() si tu veux lire depuis un fichier de config, sinon env() ici est OK
        $this->base = rtrim(env('FRAPPE_URL', 'http://erpnext.localhost:8000'), '/');
    }

    /**
     * Envoie une requête POST à l’API Frappe
     */
    public function post(string $method, array $payload): array
    {
        $url = "{$this->base}/{$method}";
        $response = Http::post($url, $payload);

        Log::debug("Réponse brute ERPNext (POST {$method}) : " . $response->body());
        Log::debug("Content-Type : " . $response->header('Content-Type'));

        return $response->json();
    }

    /**
     * Envoie une requête GET à l’API Frappe avec le SID stocké en session
     */
    public function get(string $method, array $query = []): array
{
    $url = "{$this->base}/{$method}";
    $sid = Session::get('sid');

    $response = Http::withHeaders([
        'Cookie' => "sid={$sid}",
    ])->get($url, $query);

    if ($response->successful()) {
        return $response->json() ?? []; // au cas où json() retourne null
    }

    // Log pour debug
    logger()->error("Erreur HTTP lors de l'appel à Frappe : " . $response->status());
    logger()->error("Réponse : " . $response->body());

    return [];
}

}
