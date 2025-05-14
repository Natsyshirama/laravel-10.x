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
    

    
   
    
    public function updateAndSubmitItems($quotationName, array $items)
    {
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
    
        // 1. Récupération du devis
        $quotation = $this->getQuotationDetails($quotationName);
    
        if ($quotation['docstatus'] != 0) {
            throw new \Exception("Impossible de modifier un devis soumis ou annulé.");
        }
    
        // 2. Mise à jour des items
        foreach ($items as $updatedItem) {
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
                throw new \Exception("Item non trouvé: " . $updatedItem['item_code_originale']);
            }
        }
    
        // 3. Envoi des modifications
        $updateResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->put($this->baseUrl . '/api/resource/Supplier Quotation/' . $quotationName, [
            'items' => $quotation['items']
        ]);
    
        
        if (!$updateResponse->successful()) {
            throw new \Exception("Erreur lors de la mise à jour: " . $updateResponse->body());
        }
    
        // 4. Soumission du devis
        $submitResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
            'doc' => $this->getQuotationDetails($quotationName) // Recharger les dernières données
        ]);
    
        if (!$submitResponse->successful()) {
            throw new \Exception("Erreur lors de la soumission: " . $submitResponse->body());
        }
    
        return $submitResponse->json();
    }

}