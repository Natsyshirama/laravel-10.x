<?php
namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CommandeClientAPI{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getAllCommande(array $filtre = []){

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
        ])->get($this->baseUrl . '/api/resource/Sales Order', $parametre);
        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }

    public function getCommandeClientDetails($name){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Sales Order/' . $name);
       
        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la récupération des factures : " . $response->body());
        }
        return $response->json('data');
    }

    public function validerCommande($name){
        $sid = Session::get('sid');
    
        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        //alaina alou ilay validena

        $docResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
            ])->get($this->baseUrl . '/api/resource/Sales Order/' . $name);
        
        if(!$docResponse->successful()){
            throw new \Exception("Erreur lors de la recuperation du commande" . $docResponse->body());
        }

        //validena ref azo

        $validResponse =Http::withHeaders([
             'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
            'doc' => $docResponse->json('data')
        ]);
        if (!$validResponse->successful()) {
            throw new \Exception("Erreur lors de la validation : " . $validResponse->body());
        }

        return $validResponse->json('message') ?? $validResponse->json('data');

    }
}