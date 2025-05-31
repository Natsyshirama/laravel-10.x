<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SalaireAPI{

    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getInfoSalaryEmployee($name){
        $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }
   

    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Structure Assignment', [
            'filters' => json_encode([
                ['employee', '=', $name]
            ]),
            'fields' => json_encode([
                'name',
                'employee',
                'employee_name',
                'department',
                'designation',
                'salary_structure',
                'from_date',
                'income_tax_slab',
                'company',
                'payroll_payable_account',
                'currency',
                'base',
                'variable',
                'taxable_earnings_till_date',
                'tax_deducted_till_date'
            ])
        ]);
        return $response->json('data');
    }catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des information sur les structures salariales', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
 }

 public function getListeFichePaieEmployee($name){
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }
    $fields = ['name','employee','employee_name','status','department','company','posting_date','salary_structure'];
    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Slip', [
            'filters' => json_encode([
                ['employee', '=', $name]
            ]),
            'fields' => json_encode($fields)
        ]);
        if ($response->successful()) {
            return $response->json('data') ?? [];
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            return [];
        }
            }catch (\Exception $e) {
        Log::error('Erreur lors de la récupération liste fiche de paie', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
 }
 
}