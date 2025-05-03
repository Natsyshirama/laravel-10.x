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
}