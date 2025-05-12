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
    
        $docResponse = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->get($this->baseUrl . '/api/resource/Purchase Invoice/' . $name);
    
        if (!$docResponse->successful()) {
            throw new \Exception("Erreur lors de la récupération de la facture : " . $docResponse->body());
        }
    
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
    
        //
        $facture = $this->getFacturesAchatDetails($factureName);
    
        if ($facture['docstatus'] == 0) {
            throw new \Exception("La facture est en brouillon. Veuillez la valider avant le paiement.");
        }
    
        $paymentData = [
            'payment_type' => 'Pay',
            'party_type' => 'Supplier',
            'party' => $facture['supplier'],
            'posting_date' => date('Y-m-d'),
            'company' => $facture['company'],
            'paid_from' => "CashB - EM", 
            'paid_to' => "Creditors - EM",
            'paid_amount' => $facture['grand_total'],
            'received_amount' => $facture['grand_total'],
            'reference_no' => 'PAY-' . time(),
            'reference_date' => date('Y-m-d'),
            'mode_of_payment' => 'Cash',            
            'source_exchange_rate' => 1, 
            'target_exchange_rate' => 1, 
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
    
        if ($facture['currency'] !== $this->getCompanyCurrency($facture['company'])) {
            $paymentData['source_exchange_rate'] = $this->getExchangeRate(
                $facture['currency'],
                $this->getCompanyCurrency($facture['company'])
            );
        }
        
    
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/resource/Payment Entry', $paymentData);
    
        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la création du paiement : " . $response->body());
        }
    
       
          $paymentEntry = $response->json('data');

         $submitResponse = Http::withHeaders([
             'Cookie' => 'sid=' . $sid,
             'Accept' => 'application/json',
             'Content-Type' => 'application/json'
         ])->post($this->baseUrl . '/api/method/frappe.client.submit', [
             'doc' => $paymentEntry,
         ]);
         if (!$submitResponse->successful()) {
             throw new \Exception("Erreur lors de la validation du paiement : " . $submitResponse->body());
       }
          
         return[
             'payment_entry' => $paymentEntry,
             'valid_payment' => $submitResponse->json('data'),
         ];
        //return $response->json('data');
    
    }
    
    private function getPaidFromAccount($company)
    {
        return 'CashB - ' . $this->getCompanyAbbr($company);
    }
    
    private function getPaidToAccount($company)
    {
        return 'Creditors - ' . $this->getCompanyAbbr($company);
    }
    
    private function getCompanyCurrency($company)
    {
        return 'EUR'; 
    }
    
    private function getExchangeRate($fromCurrency, $toCurrency)
    {
        return 1.0;
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