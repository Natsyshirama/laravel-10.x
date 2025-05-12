<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FournisseurAPI
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://erpnext.localhost:8000';
    }

    public function getAllFournisseurs()
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Supplier', [
            'fields' => json_encode(['name'])
        ]);

        if (!$response->successful()) {
            throw new \Exception("Erreur API Fournisseur : " . $response->body());
        }

        return $response->json('data');
    }
    


    
}
