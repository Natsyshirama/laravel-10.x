<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Services\EmployeeAPI;
use App\Services\SalaryStructur;

class Assignement{
    protected $baseUrl;
    protected $employeeAPI;
    protected $salaryStr;

    public function __construct(EmployeeAPI $employeeAPI,SalaryStructur $salaryStr){
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
        $this->employeeAPI = $employeeAPI;
        $this->salaryStr = $salaryStr;
    }

    public function getEmployee(){
        return $this->employeeAPI->getEmployees();
    }

    public function getSalaryStr(){
        return $this->salaryStr->getSalaryStructure();
    }


    public function ajoutAssignement(array $data){

    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/resource/Salary Structure Assignment',[
            'data' => $this->formatAssignmentData($data)
        ]);
        Log::debug('Réponse de l\'API:', ['status' => $response->status(), 'body' => $response->body()]); // Log de la réponse
        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de l'ajout Salary structur Assignment: " . $response->body());
        }
    }catch (\Exception $e) {
        Log::error('Erreur assignement salary structure', [
            'error' => $e->getMessage(),
            'data' => $data,
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

public function formatAssignmentData(array $data){
    return [
    'employee' => $data['employee'],
    'salary_structure' => $data['salary_structure'],
    'base' => $data['base'],
    'from_date' => $data['from_date'],
    
    ];
}
public function getMoyenneSalaireBase()
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Non connecté");
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Salary Structure Assignment', [
        'fields' => json_encode(['base']),
        'filters' => json_encode([
            ['docstatus', '=', 1]
        ]),
        'limit_page_length' => 1000 // adapte si beaucoup d’employés
    ]);

    if (!$response->successful()) {
        throw new \Exception("Erreur récupération salaires de base : " . $response->body());
    }

    $data = $response->json('data');

    if (empty($data)) {
        throw new \Exception("Aucun salaire de base trouvé pour le calcul.");
    }

    $total = array_sum(array_column($data, 'base'));
    $moyenne = $total / count($data);

    return round($moyenne, 2);
}


}