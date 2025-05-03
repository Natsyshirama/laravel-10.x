<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CommandeAchatAPI
{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }
    public function getStatusDispo()
{
    $sid = Session::get('sid');

    if (!$sid) {
        throw new \Exception('Non connecté');
    }

    $params = [
        'fields' => json_encode(['status']),
        'limit_page_length' => 1000, // ajustable
    ];

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Purchase Order', $params);

    if (!$response->successful()) {
        throw new \Exception("Erreur lors de la récupération des statuts : " . $response->body());
    }

    $data = $response->json('data');

    // Extraire uniquement les statuts uniques
    $statuses = collect($data)->pluck('status')->unique()->values()->all();

    return $statuses;
}


    public function getCommandesAchat(array $extraParams = [])
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $fields = [
            "name", "supplier", "supplier_name", "company",
            "status", "transaction_date", "grand_total",
            "currency"
        ];

        $params = array_merge([
            'fields' => json_encode($fields)
        ], $extraParams);

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Purchase Order', $params);

        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }
    public function getCommandeAchatDetails($name)
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Purchase Order/' . $name);

        if (!$response->successful()) {
            throw new \Exception("Erreur API : " . $response->body());
        }

        return $response->json('data');
    }
}