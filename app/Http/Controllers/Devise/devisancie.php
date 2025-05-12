<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DevisSuppAPI
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getQuotations(array $extraParams = [])
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $fields = [
            "name", "supplier", "supplier_name", "company",
            "status", "transaction_date", "valid_till",
            "grand_total", "currency"
        ];

        $params = array_merge([
            'fields' => json_encode($fields)
        ], $extraParams);

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Supplier Quotation', $params);

        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }

    public function getQuotationDetails($name)
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Supplier Quotation/' . $name);

        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }

    public function updateItemDetails($quotationName, $updatedItem)
    {
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $quotation = $this->getQuotationDetails($quotationName);

        if ($quotation['docstatus'] != 0) {
            throw new \Exception("Impossible de modifier un devis soumis ou annulé.");
        }

        $found = false;
        foreach ($quotation['items'] as &$item) {
            if ($item['item_code'] === $updatedItem['item_code_originale']) {
                $item['item_code'] = $updatedItem['item_code'];
                $item['qty'] = $updatedItem['qty'];
                $item['rate'] = $updatedItem['rate'];
                $item['uom'] = $updatedItem['uom'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \Exception("Item non trouvé dans le devis.");
        }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->put($this->baseUrl . '/api/resource/Supplier Quotation/' . $quotationName, [
            'items' => $quotation['items']
        ]);

        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        // Validation du devis après mise à jour
        $getDevisReponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Supplier Quotation/' . $quotationName);
    
        if (!$getDevisReponse->successful()) {
            throw new \Exception("Erreur lors de la récupération du devis : " . $getDevisReponse->body());
        }
    
        $submitResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
            'doc' => $getDevisReponse->json('data')
        ]);
    
        if (!$submitResponse->successful()) {
            throw new \Exception("Erreur lors de la validation du devis : " . $submitResponse->body());
        }
    
        return $submitResponse->json();
    }
}