<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
 public function getFichePaieDetails($name)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }
    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Slip/' . $name);
        
        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de la récupération des détails de la fiche de paie : " . $response->body());
        }
    } catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des détails de la fiche de paie', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
} 


public function getSalaryByMonth($mois = null)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Session non valide (sid manquant)");
    }

    $filters = [['docstatus', '=', 1]];

    if ($mois) {
        $startDate = $mois . '-01';
        $endDate = Carbon::parse($startDate)->endOfMonth()->format('Y-m-d');

        $filters[] = ['start_date', '>=', $startDate];
        $filters[] = ['start_date', '<=', $endDate];
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Salary Slip', [
        'filters' => json_encode($filters),
        'fields' => json_encode(['name', 'employee', 'employee_name', 'net_pay']),
        'limit_page_length' => 1000
    ]);

    $slips = $response->json('data');
    $resultats = [];
    $total_gains = 0;
    $total_deductions = 0;
    $total_net = 0;

    foreach ($slips as $slip) {
        $details = $this->getFichePaieDetails($slip['name']);

        $gains = collect($details['earnings'] ?? [])->sum('amount');
        $deductions = collect($details['deductions'] ?? [])->sum('amount');
        $net = $details['net_pay'] ?? 0;

        $gainDetails = collect($details['earnings'] ?? [])
            ->map(fn($item) => $item['salary_component'] . ': ' . number_format($item['amount'], 2, ',', ' ') . ' €')
            ->implode('<br>');

        $deductionDetails = collect($details['deductions'] ?? [])
            ->map(fn($item) => $item['salary_component'] . ': ' . number_format($item['amount'], 2, ',', ' ') . ' €')
            ->implode('<br>');

        $resultats[] = [
            'employee_name' => $slip['employee_name'],
            'gains' => $gains,
            'gainDetails' => $gainDetails,
            'deductions' => $deductions,
            'deductionDetails' => $deductionDetails,
            'net_pay' => $net
        ];

        $total_gains += $gains;
        $total_deductions += $deductions;
        $total_net += $net;
    }

    return [
        'mois' => $mois,
        'resultats' => $resultats,
        'total_gains' => $total_gains,
        'total_deductions' => $total_deductions,
        'total_net' => $total_net
    ];
}
}