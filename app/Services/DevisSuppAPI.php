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

    public function cloneQuotation($originalName)
    {
        $sid = Session::get('sid');
        
        // 1. Récupérer le devis original (maintenant annulé)
        $original = $this->getQuotationDetails($originalName);
    
        // 2. Générer un nouveau nom
        $newName = $this->generateNewQuotationName($originalName);
    
        // 3. Préparer les données du clone
        $data = [
            'supplier' => $original['supplier'],
            'transaction_date' => $original['transaction_date'],
            'valid_till' => $original['valid_till'],
            'items' => array_map(function($item) {
                return [
                    'item_code' => $item['item_code'],
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    // autres champs nécessaires...
                ];
            }, $original['items']),
            'amended_from' => $originalName // Important: référence à l'original
        ];
    
        // 4. Créer le nouveau devis
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->post($this->baseUrl."/api/resource/Supplier Quotation", $data);
    
        if (!$response->successful()) {
            throw new \Exception("Échec du clonage: ".$response->body());
        }
    
        return $newName;
    }
    
    protected function generateNewQuotationName($originalName)
    {
        $sid = Session::get('sid');
        
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl."/api/resource/Supplier Quotation", [
            'filters' => json_encode([['amended_from', '=', $originalName]])
        ]);
    
        $count = count($response->json()['data'] ?? []);
        return $originalName.'-'.($count + 1);
    }

    public function updateQuotationItems($quotationName, array $items)
    {
        $sid = Session::get('sid');

        // Vérifie si le devis est annulé
        $quotation = $this->getQuotationDetails($quotationName);
        if ($quotation['docstatus'] == 2) {
            throw new \Exception("Impossible de modifier le devis '$quotationName' car il est annulé.");
        }

        $data = [
            'items' => $items
        ];

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->put($this->baseUrl . "/api/resource/Supplier Quotation/{$quotationName}", $data);

        if (!$response->successful()) {
            throw new \Exception("Erreur mise à jour : " . $response->body());
        }

        return $response->json('data');
    }

    public function submitQuotation($quotationName)
    {
        $sid = Session::get('sid');
    
        // D'abord rafraîchir le document
        $quotation = $this->getQuotationDetails($quotationName);
    
        // Ensuite soumettre le devis
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->post($this->baseUrl . "/api/method/frappe.client.submit", [
            'doc' => json_encode([
                'doctype' => 'Supplier Quotation',
                'name' => $quotationName,
                'modified' => $quotation['modified'] // Inclure le timestamp actuel
            ])
        ]);
    
        if (!$response->successful()) {
            throw new \Exception("Erreur soumission : " . $response->body());
        }
    
        return $response->json('message');
    }
    public function cancelQuotation($quotationName)
    {
        $sid = Session::get('sid');
    
        $quotation = $this->getQuotationDetails($quotationName);
        if ($quotation['docstatus'] == 2) {
            throw new \Exception("Le devis '$quotationName' est déjà annulé.");
        }
    
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->post($this->baseUrl . "/api/method/frappe.client.cancel", [
            'doctype' => 'Supplier Quotation',
            'name' => $quotationName
        ]);
    
        if (!$response->successful()) {
            throw new \Exception("Erreur annulation : " . $response->body());
        }
    
        return $response->json('message');
    }
}