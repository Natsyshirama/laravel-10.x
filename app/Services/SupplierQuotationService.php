<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;


class SupplierQuotationService{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
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
    public function getAllItem(){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response =Http::withHeaders([
            'Cookie'=> 'sid=' .$sid
        ])->get($this->baseUrl . 'api/resource/Item' , [
            'fields' => json_encode(['name'])
        ]);
        if (!$response->successful()) {
            throw new \Exception("Erreur API Fournisseur : " . $response->body());
        }
        return $response->json('data');
    }

    public function getAllWarehouse(){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response =Http::withHeaders([
            'Cookie'=> 'sid=' .$sid
        ])->get($this->baseUrl . 'api/resource/Warehouse' , [
            'fields' => json_encode(['name'])
        ]);
        if (!$response->successful()) {
            throw new \Exception("Erreur API Fournisseur : " . $response->body());
        }
        return $response->json('data');
    }


    
}