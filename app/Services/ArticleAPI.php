<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ArticleAPI
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getAllItems()
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }

        $fields = ['name', 'item_code','item_name'];
        $params = [
            'fields' => json_encode($fields),
            'limit_page_length' => 1000
        ];

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Item', $params);

        if (!$response->successful()) {
            throw new \Exception("Erreur lors de la récupération des articles : " . $response->body());
        }

        return $response->json('data');
    }
}
