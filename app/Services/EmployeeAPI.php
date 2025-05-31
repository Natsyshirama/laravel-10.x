<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class EmployeeAPI{

    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getDepartement(){
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }

            try{
                $response = Http::withHeaders([
                    'Cookie' => 'sid=' . $sid
                ])->get($this->baseUrl . '/api/resource/Department', [
                    'fields' => json_encode(['name'])
                ]);

                if (!$response->successful()) {
                    throw new \Exception("Erreur API Fournisseur : " . $response->body());
                }

                return $response->json('data');
            }catch (\Exception $e) {
                Log::error('Erreur lors de la récupération des départements', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
    
    }

    public function getEmployees(array $filtre = []){
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $fields = [
            "name", "first_name", "department","designation","company","status"        ];
    
        $parametre = array_merge([
            'fields'=> json_encode($fields)
        ], $filtre);
    
    try{
        $reponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Employee', $parametre);
        if (!$reponse->successful()) {
            throw new \Exception("Erreur API : " . $reponse->body());
        }
        return $reponse->json('data');

    }catch (\Exception $e) {
        Log::error('Erreur création devis', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
   
}

public function getEmployeeDetails($name){
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Employee/' . $name);

        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');

    }catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des détails de l\'employé', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
}