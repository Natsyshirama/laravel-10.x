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

    public function createSupplierQuotation(array $data)
    {
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
    
        // Formatage des items
        $items = [];
        foreach ($data['items'] as $item) {
            $items[] = [
                'item_code' => $item['item_code'],
                'qty' => $item['qty'],
                'rate' => $item['rate'],
                'warehouse' => 'All Warehouse - EM',
                'uom' => $item['uom'] ?? 'Unit'
            ];
        }
    
        $payload = [
            'supplier' => $data['supplier'],
            'transaction_date' => $data['transaction_date'],
            'valid_till' => $data['valid_till'],
            'items' => $items,
            'doctype' => 'Supplier Quotation'
        ];
    
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/resource/Supplier Quotation', $payload);
    
        if (!$response->successful()) {
            throw new \Exception("Erreur API: " . $response->body());
        }
    
        return $response->json();
    }
    
}