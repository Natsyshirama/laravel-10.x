<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;
use Carbon\Carbon;
use App\Services\HistoriqueService;


class UpdateService{

    protected $baseUrl;
    protected $histoService;
    public function __construct(HistoriqueService $histoService)
    {
        $this->histoService = $histoService;
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

    //**MODIFIER SALAIRE */

//component modifier
public function getComponent(){
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get($this->baseUrl . '/api/resource/Salary Component', [
             'fields' => json_encode(['name'])
        ] );

        if ($response->successful()) {
            return $response->json('data');
        } else {
            Log::error('Erreur API Frappe', ['response' => $response->body()]);
            throw new \Exception("Erreur lors de la récupération Component type Gains: " . $response->body());
        }
}



public function getEmployees($component, $operator, $amount)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Non connecté");
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get(env('FRAPPE_URL') . '/api/resource/Salary Slip', [
        'fields' => json_encode([
            'name', 'employee', 'employee_name', 'salary_structure', 'start_date'
        ]),
        'filters' => json_encode([
            ['docstatus', '=', 1] 
        ]),

        'limit_page_length' => 1000
    ]);

    if (!$response->successful()) {
        throw new \Exception("Erreur lors de la récupération des Salary Slips : " . $response->body());
    }

    $salarySlips = $response->json('data');
    $listeEmployees = [];

    foreach ($salarySlips as $slip) {
        $detailResp = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
        ])->get(env('FRAPPE_URL') . '/api/resource/Salary Slip/' . $slip['name']);

        if (!$detailResp->successful()) {
            continue;
        }

        $detail = $detailResp->json('data');
        $details = array_merge($detail['earnings'] ?? [], $detail['deductions'] ?? []);

        foreach ($details as $line) {
            if ($line['salary_component'] === $component) {
                $val = floatval($line['amount']);
                if (($operator === '>' && $val > $amount) || ($operator === '<' && $val < $amount)) {
                    $listeEmployees[] = [
                        'name' => $slip['name'],
                        'employee' => $detail['employee'],
                        'employee_name' => $detail['employee_name'],
                        'salary_structure' => $detail['salary_structure'],
                        'start_date' => $detail['start_date'],
                        'details' => $details
                    ];
                    break;
                }
            }
        }
    }

    return $listeEmployees;
}

public function getSalaryStructureAssg($employe){
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }
    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid
            ])->get(env('FRAPPE_URL') . '/api/resource/Salary Structure Assignment',  [
                'filters' => json_encode([
                    ['employee', '=', $employe],
                    ['docstatus', '<', 2],
                    ['from_date', '<', now()->toDateString()]
                ]),
                'fields' => json_encode([
                'name', 'employee','employee_name' ,'salary_structure', 'base', 'from_date'
            ]),
            
            ]); 


    if (!$response->successful()) {
        throw new \Exception("Erreur API : " . $response->body());
    }
    return $response->json()['data'];
    }catch (\Exception $e) {
        Log::error('Erreur lors du recuperation SSA', [
            
            'error' => $e->getMessage()
        ]);
    }

}



public function updateSalaire($component,$operator,$amount, $methode,$pourcentage)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception('Non connecté');
    }

    //tableau empployee
    $employes = $this->getEmployees($component,$operator,$amount); 
    if (empty($employes)) {
        throw new \Exception("Aucun employé trouvé pour le filtre donné.");
    }

    $headers = [
        'Cookie' => 'sid=' . $sid,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    $url = $this->baseUrl . '/api/resource/';

    foreach ($employes as $employe) {
        $ssas = $this->getSalaryStructureAssg($employe['employee']);

        foreach($ssas as $ssa){

        try {
            $employesAn = $ssa['employee_name'];
            $ancienSSA = $ssa['name']; 
            $from_date = $ssa['from_date'];
            $oldBase= $ssa['base'];
            $insert = $this->histoService->insertHistorique($employesAn, $oldBase, $from_date);


            if($methode === 'plus'){
                $newBase = $oldBase + ($oldBase * $pourcentage/100);
            }elseif($methode === 'moins'){
                $newBase = $oldBase - ($oldBase * $pourcentage/100);
            }else{
                throw new \Exception("Methode inconnue : $methode");
            }
            $annulerPayload = [
                'docstatus' => 2 
            ];
            //cancel
            Http::withHeaders($headers)
                ->put($this->baseUrl . '/api/resource/Salary Structure Assignment/' . $ancienSSA, ['data' => $annulerPayload]);
            
            $nouveauPayload = [
                'employee' => $employe['employee'],
                'salary_structure' => $employe['salary_structure'],
                'base' => round($newBase, 2),
                'from_date' => $from_date,
                'amended_from' => $ancienSSA,
                'docstatus' => 1 
            ];

            //creation new SSA
            $response = Http::withHeaders($headers)
                ->post($url . 'Salary Structure Assignment', ['data' => $nouveauPayload]);

            if (!$response->successful()) {
                Log::error('Erreur lors de la création du nouveau SSA', [
                    'employe' => $employe['employee'],
                    'response' => $response->body()
                ]);
            }

        try{
            //annuler slip
            $salarSlips = $employe['name'];

            Http::withHeaders($headers)
                ->put($this->baseUrl . '/api/resource/Salary Slip/' . $salarSlips, ['docstatus' => 2]);
            
            //delete
            Http::withHeaders($headers)
            ->delete($this->baseUrl . '/api/resource/Salary Slip/' . $salarSlips);
            Log::info('Salary Slip annule pour employee : ' . $employe['employee']);

            //cree
            $startDate = $employe['start_date'];
            $endDate = Carbon::parse($startDate)->endOfMonth()->toDateString();
            $newSlipPayload = [
                'employee' => $employe['employee'],
                'salary_structure' => $employe['salary_structure'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'creation' => now()->toDateTimeString(),
                'docstatus' => 1

            ];

            $slipCreation = Http::withHeaders($headers)
                ->post($this->baseUrl . '/api/resource/Salary Slip', ['data' => $newSlipPayload]);
            if (!$slipCreation->successful()) {
                Log::error('Erreur lors de la creation du Salary Slip', [
                    'employe' => $employe['employee'],
                    'response' => $slipCreation->body()
                ]);
            } else {
                Log::info('Salary Slip cree avec succsse : ' . $employe['employee']);
            }
        }catch (\Exception $ex) {
            Log::error('Erreur mise à jour Salary Slip apres SSA', [
                'employe' => $employe['employee'],
                'error' => $ex->getMessage()
            ]);
        }


        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de employee', [
                'employe' => $employe['employee'],
                'error' => $e->getMessage()
            ]);
        }
    }
    }
    return count($employes);
}


}