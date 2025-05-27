<?php
namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LivraisonAPI{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getAllLivraison(array $filtre = []){

        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $fields = [
            "name", "customer_name","posting_date","posting_time","total","currency","status"
        ];
        $parametre = array_merge([
            'fields'=> json_encode($fields)
        ], $filtre);

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Delivery Note', $parametre);
        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }

    public function getLivraisonDetails($name){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Delivery Note/' . $name);
       
        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la récupération des factures : " . $response->body());
        }
        return $response->json('data');
    }
}