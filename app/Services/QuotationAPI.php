<?php
namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class QuotationAPI{

    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getDevis(array $filtre = []){
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $fields = [
            "name", "customer_name","transaction_date","total","currency","status"
        ];

        $parametre = array_merge([
            'fields'=> json_encode($fields)
        ], $filtre);

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Quotation', $parametre);
        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }
    public function getQuotationDetails($name){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Quotation/' . $name);
       
        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la récupération des factures : " . $response->body());
        }
        return $response->json('data');

    }
}