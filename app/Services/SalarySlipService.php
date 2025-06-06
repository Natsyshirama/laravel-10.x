<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Services\SalaireAPI;

class SalarySlipService{

    protected $salaire;
    protected $baseUrl;
    public function __construct(SalaireAPI $salaire)
    {
        $this->salaire = $salaire;  
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    public function getSalarySummaryByYear($annee)
    {
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception("Session non valide");
        }
    
        $start = $annee . '-01-01';
        $end = $annee . '-12-31';
    
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Slip', [
            'filters' => json_encode([
                ['start_date', '>=', $start],
                ['start_date', '<=', $end],
                ['docstatus', '=', 1]
            ]),
            'fields' => json_encode(['name', 'start_date', 'net_pay']),
            'limit_page_length' => 1000
        ]);
    
        $slips = $response->json('data');
        $moisData = [];
        $componentsList = [];
    
        foreach ($slips as $slip) {
            $mois = Carbon::parse($slip['start_date'])->format('F');
            $details = $this->salaire->getFichePaieDetails($slip['name']);
            $net = $details['net_pay'] ?? 0;
    
            if (!isset($moisData[$mois])) {
                $moisData[$mois] = [
                    'net_pay' => 0,
                    'total_gains' => 0,
                    'total_deductions' => 0,
                    'components' => []
                ];
            }
    
            $moisData[$mois]['net_pay'] += $net;
            $moisData[$mois]['total_gains'] += collect($details['earnings'] ?? [])->sum('amount');
            $moisData[$mois]['total_deductions'] += collect($details['deductions'] ?? [])->sum('amount');
           
            foreach ($details['earnings'] ?? [] as $earning) {
                $comp = $earning['salary_component'];
                $amount = $earning['amount'];
    
                $moisData[$mois]['components'][$comp] = ($moisData[$mois]['components'][$comp] ?? 0) + $amount;
                $componentsList[$comp] = true;
            }
            foreach ($details['deductions'] ?? [] as $deduction) {
                $comp = $deduction['salary_component'];
                $amount = $deduction['amount'];
    
                $moisData[$mois]['components'][$comp] = ($moisData[$mois]['components'][$comp] ?? 0) - $amount;
                $componentsList[$comp] = true;
            }
        }
    
        return [
            'annee' => $annee,
            'moisData' => $moisData,
            'components' => array_keys($componentsList)
        ];
    }
    
}