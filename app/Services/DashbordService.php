<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class DashbordService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getDashboard()
    {
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception("Session expirée.");
        }
    
        $headers = ['Cookie' => 'sid=' . $sid];
    
        // Récupérer les devis
        $devis = Http::withHeaders($headers)
            ->get($this->baseUrl . '/api/resource/Supplier Quotation?fields=["name","grand_total"]&limit_page_length=1000');
    
        // Récupérer les commandes
        $commandes = Http::withHeaders($headers)
            ->get($this->baseUrl . '/api/resource/Purchase Order?fields=["name","grand_total"]&limit_page_length=1000');
    
        //recuperer les facture
        $factures = Http::withHeaders($headers)
            ->get($this->baseUrl. '/api/resource/Purchase Invoice?fields=["name","grand_total"]');
        
        if (!$devis->successful() || !$commandes->successful()) {
            throw new \Exception("Erreur de récupération des données.");
        }
        if(!$factures->successful()){
            throw new \Exception("Erreur recuperation facture");
        }
    
        $devisData = $devis['data'];
        $commandesData = $commandes['data'];
        $factureData =$factures['data'];
    
        return [
            'devis_count' => count($devisData),
            'devis_total' => array_sum(array_column($devisData, 'grand_total')),//sum des grand_total
            'commande_count' => count($commandesData),
            'commande_total' => array_sum(array_column($commandesData, 'grand_total')),
            'facture_count'=> count($factureData),
            'facture_total'=> array_sum(array_column($factureData, 'grand_total')),
        ];
    }
    
}