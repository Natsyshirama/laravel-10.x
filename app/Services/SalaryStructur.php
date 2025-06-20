<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;

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
public function ajoutStructure(array $data){
    

    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

    try {
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($this->baseUrl. '/api/resource/Salary Structure', [
            'data' => $this->formatStructureData($data)
        ]);
        Log::debug('Réponse de l\'API:', ['status' => $response->status(), 'body' => $response->body()]); 
        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de l'ajout Salary structur: " . $response->body());
        }
        
    }catch (\Exception $e) {
        Log::error('Erreur création devis', [
            'error' => $e->getMessage(),
            'data' => $data,
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
public function formatStructureData(array $data){
    return[
        'name' =>$data['name'],
        'company' => $data['company'] ?? 'My Company',
        'payroll_frequency' => $data['payroll_frequency'] ?? 'Monthly',
        'is_active' => $data['is_active'] ?? 'Yes',
        'currency' => $data['currency'] ?? 'EUR',
        'docstatus'=> 1,
        'earnings' => $this->formatEarnings($data['earnings'] ?? []),
        'deductions' => $this->formatDeductions($data['deductions'] ?? []),
    ];
}

public function formatEarnings(array $earnings){
    $formatted = [];
    foreach ($earnings as $earning) {
        $formatted[] = [
            'doctype' => 'Salary Detail', 
            'salary_component' => $earning['salary_component'],
            'amount' =>  0,
            'is_taxable' => 1,
            'depends_on_payment_days'=> 0,
            'amount_based_on_formula'=>1,
            'formula' => $earning['formula'] ?? '',
        ];
    }
    return $formatted;
}
public function formatDeductions(array $deductions){
    return array_map(function ($deduction) {
        return [
            'doctype' => 'Salary Detail', 
            'salary_component' => $deduction['salary_component'],
            'amount' => 0,
            'is_taxable' => 1,
            'depends_on_payment_days'=> 0,
            'amount_based_on_formula'=>1,
            'formula' => $deduction['formula'] ?? '',
        ];
    }, $deductions);
}



}