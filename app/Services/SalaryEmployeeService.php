<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;
use Carbon\Carbon;
class SalaryEmployeeService{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }

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
//update
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
//update
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
                'name', 'employee', 'salary_structure', 'base', 'from_date'
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
            $ancienSSA = $ssa['name']; 
            $from_date = $ssa['from_date'];
            $oldBase= $ssa['base'];
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

public function getSalaryStr($employee)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Non connecté");
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Salary Structure Assignment', [
        'fields' => json_encode(['salary_structure']),
        'filters' => json_encode([
            ['employee', '=', $employee],
            ['docstatus', '=', 1]
        ]),
        'limit_page_length' => 1,
        'order_by' => 'creation desc'
    ]);

    if ($response->successful()) {
        $data = $response->json('data');
        return count($data) > 0 ? $data[0]['salary_structure'] : null;
    } else {
        Log::error('Erreur récupération Salary Structure actuel', ['response' => $response->body()]);
        throw new \Exception("Impossible de récupérer la Salary Structure actuelle.");
    }
}

public function verifieMois($employee, $start_date, $end_date)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Non connecté");
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Salary Structure Assignment', [
        'filters' => json_encode([
            ['employee', '=', $employee],
            ['from_date', '>=', $start_date],
            ['from_date', '<=', $end_date],
            ['docstatus', '<', 2]
        ]),
        'fields' => json_encode(['name']),
        'limit_page_length' => 1
    ]);

    if ($response->successful()) {
        return count($response->json('data')) > 0;
    }

    Log::error('Erreur vérification SSA existant', ['response' => $response->body()]);
    throw new \Exception("Erreur lors de la vérification des SSA existants.");
}

public function getSalaireBase($employee)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Non connecté");
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
    ])->get($this->baseUrl . '/api/resource/Salary Structure Assignment', [
        'filters' => json_encode([
            ['employee', '=', $employee],
            ['docstatus', '=', 1]
            
        ]),
        'fields' => json_encode(['base']),
        'limit_page_length' => 1,
        'order_by' => 'creation desc'//decroissante

    ]);

    if ($response->successful()) {
        $data = $response->json('data');
        return count($data) > 0 ? $data[0]['base'] : null;
    }


    Log::error('Erreur recuperation Salaire base', ['response' => $response->body()]);
    throw new \Exception("Erreur lors de la vérification des SSA existants.");
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
        'limit_page_length' => 1000 
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


public function genereSalarySA($employee, $base_salary, $from_date, $to_date, $force=false)
{
    $sid = Session::get('sid');
    if (!$sid) {
        throw new \Exception("Non connecté");
    }

    if (empty($base_salary)) {
        $base_salary = $this->getSalaireBase($employee);
        if (!$base_salary) {
            throw new \Exception("Absence de base salary pour l'employé : " . $employee);
        }
    }

    $salary_structure = $this->getSalaryStr($employee);
    if (!$salary_structure) {
        throw new \Exception("Aucune Salary Structure trouvée pour cet employé.");
    }

//boucle sur chaque mois
    $start = Carbon::parse($from_date)->startOfMonth();
    $end = Carbon::parse($to_date)->endOfMonth();

    $current = $start->copy();

    $created = [];

    while ($current->lte($end)) {
        $month = $current->format('Y-m'); 
        $from = $current->copy()->startOfMonth()->toDateString();
        $to = $current->copy()->endOfMonth()->toDateString();

        $existing = $this->verifieMois($employee, $from, $to);
        if ($existing){
            if(!$force){
            Log::info("SSA déjà existant pour $employee au mois de $month, saut...");
            $current->addMonth();
            continue;
        }else{
            Log::info("SSA déjà existant pour $employee au mois de $month, mais sera ecraser");
            $ssa = Http::withHeaders([
                'Cookie' => 'sid=' . $sid,
                
            ])->get($this->baseUrl . '/api/resource/Salary Structure Assignment',[
                'filters' => json_encode([
                    ['employee', '=', $employee],
                    ['from_date', '=', $from],
                    ['docstatus', '<', 2]
                ]),
                'fields' => json_encode(['name']),
                'limit_page_length' => 1
            ]);
            
            if ($ssa->successful() && count($ssa->json('data')) > 0){
                $ssaName = $ssa->json('data')[0]['name'];
            }
            $annulerPayload = [
                'docstatus' => 2 
            ];

            $newPaypload = [
                'employee'=>$employee,
                'base' => $base_salary,
                'salary_structure' => $salary_structure,
                'from_date' => $from,
                'to_date' =>$to
            ];

            //cancel [
            $cancelResponce = Http::withHeaders( [
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'])
            ->put($this->baseUrl . '/api/resource/Salary Structure Assignment/' . $ssaName, ['data' => $annulerPayload]);
        
            //create
            $createdRespons = Http::withHeaders( [
                'Cookie' => 'sid=' . $sid,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'])
                ->post($this->baseUrl . '/api/resource/Salary Structure Assignment', ['data' => $newPaypload]);
            
                //
            if(!$createdRespons->successful())
            {
                Log::error("Échec de mise à jour SSA forcé : " . $createdRespons->body());
            }
        }
    }

        $payload = [
            'docstatus' => 1,
            'employee' => $employee,
            'salary_structure' => $salary_structure,
            'base' => $base_salary,
            'from_date' => $from,
            'to_date' => $to
        ];

        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/resource/Salary Structure Assignment', [
            'data' => $payload
        ]);

        $slipPayload = [
                'employee' => $employee,
                'salary_structure' => $salary_structure,
                'start_date' => $from,
                'end_date' => $to,
                'creation' => now()->toDateTimeString(),
                'docstatus' => 1
        ];
        $slipCreation = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/resource/Salary Slip', ['data' => $slipPayload]);

            if (!$slipCreation->successful()) {
                Log::error('Erreur lors de la creation du Salary Slip', [
                    'employe' => $employee,
                    'response' => $slipCreation->body()
                ]);
            } else {
                Log::info('Salary Slip cree avec succsse : ' . $employee);
            }

            

        if ($response->successful()) {
            $created[] = $response->json('data.name');
        } else {
            Log::error('Erreur SSA API', ['response' => $response->body(), 'mois' => $from]);
            throw new \Exception("Erreur SSA pour le mois de {$from} : " . $response->body());
        }

        $current->addMonth();
    }

    return $created;
}

}
