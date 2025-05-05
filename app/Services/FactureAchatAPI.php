<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FactureAchatAPI
{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getFacturesAchat(array $extraParams = [])
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $fields = [
            "name", "supplier", "supplier_name", "company",
            "status", "posting_date", "grand_total",
            "currency"
        ];

        $params = array_merge([
            'fields' => json_encode($fields)
        ], $extraParams);

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Purchase Invoice', $params);

        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la récupération des factures : " . $response->body());
        }

        return $response->json('data');
    }
    public function getFacturesAchatDetails($name)
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Purchase Invoice/' . $name);

        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la récupération des factures : " . $response->body());
        }

        return $response->json('data');
    }

    public function validerFacture($name)
    {
        $sid = Session::get('sid');
    
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
    
        // Vérification de l'existence du document avant soumission
        $docResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->get($this->baseUrl . '/api/resource/Purchase Invoice/' . $name);
    
        if (!$docResponse->successful()) {
            throw new \Exception("Erreur lors de la récupération de la facture : " . $docResponse->body());
        }
    
        // Validation (submit) using the correct API endpoint
        $submitResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
            'doc' => $docResponse->json('data')
        ]);
    
        if (!$submitResponse->successful()) {
            throw new \Exception("Erreur lors de la validation : " . $submitResponse->body());
        }
    
        return $submitResponse->json('message') ?? $submitResponse->json('data');
    }
    
    public function payFacture($factureName)
    {
        $sid = Session::get('sid');
    
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
    
        // Récupérer les détails de la facture
        $facture = $this->getFacturesAchatDetails($factureName);
    
        if ($facture['docstatus'] == 0) {
            throw new \Exception("La facture est en brouillon. Veuillez la valider avant le paiement.");
        }
    
        // Préparation des données pour le Payment Entry
        $paymentData = [
            'payment_type' => 'Pay',
            'party_type' => 'Supplier',
            'party' => $facture['supplier'],
            'posting_date' => date('Y-m-d'),
            'company' => $facture['company'],
            'paid_from' => $this->getPaidFromAccount($facture['company']),
            'paid_to' => $this->getPaidToAccount($facture['company']),
            'paid_amount' => $facture['grand_total'],
            'received_amount' => $facture['grand_total'],
            'reference_no' => 'PAY-' . time(),
            'reference_date' => date('Y-m-d'),
            'mode_of_payment' => 'Cash', // ou "Bank" selon configuration
            'source_exchange_rate' => 1, // Valeur par défaut pour devise identique
            'target_exchange_rate' => 1, // Valeur par défaut pour devise identique
            'references' => [
                [
                    'reference_doctype' => 'Purchase Invoice',
                    'reference_name' => $factureName,
                    'total_amount' => $facture['grand_total'],
                    'outstanding_amount' => $facture['outstanding_amount'] ?? $facture['grand_total'],
                    'allocated_amount' => $facture['grand_total'],
                ]
            ]
        ];
    
        // Si la devise est différente, ajouter le taux de change
        if ($facture['currency'] !== $this->getCompanyCurrency($facture['company'])) {
            $paymentData['source_exchange_rate'] = $this->getExchangeRate(
                $facture['currency'],
                $this->getCompanyCurrency($facture['company'])
            );
        }
        
    
        // Création du Payment Entry
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/resource/Payment Entry', $paymentData);
    
        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la création du paiement : " . $response->body());
        }
    
        // ... reste du code inchangé ...
        //validation Payment entry = Payer
    //      $paymentEntry = $response->json('data');

    //     $submitResponse = Http::withHeaders([
    //         'Cookie' => 'sid=' . $sid,
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json'
    //     ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
    //         'doc' => $paymentEntry,
    //     ]);
    //     if (!$submitResponse->successful()) {
    //         throw new \Exception("Erreur lors de la validation du paiement : " . $submitResponse->body());
    //   }
        //Validation Facture =Paid
        //$factureValider = $facture->json('data');
        // $facturSubmitResponse = Http::withHeaders([
        //     'Cookie' => 'sid=' . $sid,
        //     'Accept' => 'application/json',
        //     'Content-Type' => 'application/json'
        // ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
        //     'doc' => $factureValider,
        // ]);
    
        
    //     if (!$facturSubmitResponse->successful()) {
    //         throw new \Exception("Erreur lors de la validation du paiement : " . $submitResponse->body());
    //   }    
        // return[
        //     'payment_entry'=$response->json('data'),
        //     'valid_payment'= $submitResponse->json('data'),
        //     'valid_facture' =$facturSubmitResponse->json('data'),
        // ]
        return $response->json('data');
    
    }
    
    // Ajoutez ces nouvelles méthodes à votre service
    private function getPaidFromAccount($company)
    {
        // Implémentez la logique pour récupérer le compte de débit
        return 'Cash - ' . $this->getCompanyAbbr($company);
    }
    
    private function getPaidToAccount($company)
    {
        // Implémentez la logique pour récupérer le compte fournisseur
        return 'Creditors - ' . $this->getCompanyAbbr($company);
    }
    
    private function getCompanyCurrency($company)
    {
        // À implémenter - récupère la devise de base de la société
        return 'EUR'; // Exemple
    }
    
    private function getExchangeRate($fromCurrency, $toCurrency)
    {
        // À implémenter - récupère le taux de change actuel
        return 1.0; // Exemple pour devises identiques
    }
    private function getCompanyAbbr($company)
    {
        $parts = explode(' ', $company);
        $abbr = '';
        foreach ($parts as $part) {
            $abbr .= strtoupper($part[0]);
        }
        return $abbr;
    }
            
}