<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SalaryStructur{

    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getSalaryStructure(){
        $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }
        $fields = ['name','company','docstatus','is_active'];
    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Structure',
            ['fields' =>json_encode($fields)
        
        ]);


        if ($response->successful()) {
            return $response->json('data') ?? [];
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            return [];
        }
    }

    catch (\Exception $e) {
        Log::error('Erreur lors de la récupération Salary structure', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

    public function getSalaryStrDetails($name){
        $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Structure/' . $name );
        
        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de la récupération des détails Salary structur: " . $response->body());
        }
    
    }catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des détails de Salary structure', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }

}

public function getCompany(){
    $sid = Session::get('sid');
    

    if(!$sid){
        throw new \Exception('vous devriez connecter');
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Company', [
         'fields' => json_encode(['name'])
    ]);

    if ($response->successful()) {
        return $response->json('data');
    } else {
        Log::error('Erreur API Frappe', ['response' => $response->body()]);
        throw new \Exception("Erreur lors de la récupération Company: " . $response->body());
    }
}

public function getGain(){
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Component', [
             'fields' => json_encode(['name'])
        ],['filters' => json_encode([['type', '=', 'Earning']])] );

        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de la récupération Component type Gains: " . $response->body());
        }
}



public function getDeduction(){
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Component', [
             'fields' => json_encode(['name'])
        ],['filters' => json_encode([['type', '=', 'Deduction']])] );

        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de la récupération Component type Deduction: " . $response->body());
        }
}

public function getObjetSelection(){
    return[
        'gain' => $this->getGain(),
        'deduction' => $this->getDeduction(),
        'company' => $this->getCompany()
    ];

}
}