<?php
namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
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

    public function ajoutQuotationCustomer(array $data)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }
   
    try {
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($this->baseUrl. '/api/resource/Quotation', [
            'data' => $this->formatQuotationData($data)
        ]);

        if (!$response->successful()) {
            $error = $response->json();
            throw new \Exception($error['message'] ?? "Erreur API Client : " . $response->body());
        }

        $result = $response->json('data');
        
        if (!isset($result['name'])) {
            throw new \Exception('La réponse de l\'API ne contient pas de nom de devis');
        }

        return $result;
    } catch (\Exception $e) {
        Log::error('Erreur création devis', [
            'error' => $e->getMessage(),
            'data' => $data,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    public function formatQuotationData(array $data){
        return [
            'quotation_to' =>  'Customer',
            'customer_name' => $data['customer_name'],
            'transaction_date' => $data['transaction_date'] ?? now()->format('Y-m-d'),
            'valid_till' => $data['valid_till'] ?? date('Y-m-d', strtotime('+1 days')),
            'order_type' => $data['order_type'] ?? 'Sales',
            'status' => $data['status'] ?? 'Draft',
            'conversion_rate' => $data['conversion_rate'] ?? 1,
            'selling_price_list' => $data['selling_price_list'] ?? 'Standard Selling',            
            'items' => $this->formatItems($data['items'] ?? []),
            'doctype' => 'Quotation',
            'currency' => $data['currency'] ?? 'USD',
            'company' => $data['company'] ?? 'E mark'
        ];
    } 
    
    public function formatItems(array $items){
        return array_map(function ($item) {
            return [
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'] ?? null,
                'description' => $item['description'] ?? null,
                'qty' => $item['quantity'] ?? 1,
                'uom' => $item['uom'] ?? 'Unit',
                'rate' => $item['rate'] ?? 0,
                'amount' => ($item['quantity'] ?? 1) * ($item['rate'] ?? 0),
                'warehouse' => $item['warehouse'] ?? null,
                'delivery_date' => $item['delivery_date'] ?? null
            ];
        }, $items);
    }

    public function getObjetSelection(){
        $sid = Session::get('sid');
        return[
            'customer'=> $this->getCustomers($sid),
            'items' => $this->getItems($sid),
        ];
    }
    public function getCustomers($sid){
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Customer', [
            'fields' => json_encode(['name','customer_name'])
        ]);
        if (!$response->successful()) {
            throw new \Exception("Erreur API Client : " . $response->body());
        }
        return $response->json('data');
    }
    public function getItems($sid){
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Item', [
            'fields' => json_encode(['name','item_name', 'item_code'])
        ]);
        if (!$response->successful()) {
            throw new \Exception("Erreur API Item : " . $response->body());
        }
        return $response->json('data');
    }
    public function validerQuotation($name){
        $sid = Session::get('sid');
    
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
    
        $docResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->get($this->baseUrl . '/api/resource/Quotation/' . $name);
        if (!$docResponse->successful()) {
            throw new \Exception("Erreur lors de la récupération du devis : " . $docResponse->body());
        }
        $submitResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
            'doc' => $docResponse->json('data')
        ]);

        if (!$submitResponse->successful()) {
            throw new \Exception("Erreur lors de la validation : " . $submitResponse->body());
        }
    
        return $submitResponse->json('message') ?? $submitResponse->json('data');
    
    }
}