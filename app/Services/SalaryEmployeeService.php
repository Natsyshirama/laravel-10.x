<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;
use App\Models\Reduction\ReductionModel;

use Carbon\Carbon;
class SalaryEmployeeService{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }
//**GENERER SALAIRE */
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


public function getReductionMois(string $mois): ?float
{
    $reduction = ReductionModel::where('mois', $mois)->first();
    return $reduction ? (float) $reduction->valeur : null;
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
        //appliquer le reduction sur salaire base
        $mois = $current->copy()->startOfMonth()->format('Y-m-d');

        $reduction = $this->getReductionMois($mois);
        $salary_reduit = $base_salary;

        if ($reduction !== null) {
            $salary_reduit = round($base_salary * (1 + $reduction / 100), 2);
            if ($reduction >= 0) {
                $variation = "augmentation";
            } else {
                $variation = "réduction";
            }
            
            Log::info("{$variation} appliquée pour le mois {$month}: {$reduction}% (salaire ajusté: {$salary_reduit})");
                    }

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

            $oldSalarySlip = Http::withHeaders([
                'Cookie' => 'sid=' . $sid,
                
            ])->get($this->baseUrl . '/api/resource/Salary Slip',[
                'filters' => json_encode([
                    ['employee', '=', $employee],
                    ['start_date', '=', $from],
                    ['docstatus', '<', 2]
                ]),
                'fields' => json_encode(['name']),
                'limit_page_length' => 1
            ]);

            if ($oldSalarySlip->successful() && count($oldSalarySlip->json('data')) > 0){
                $salarySlipName = $oldSalarySlip->json('data')[0]['name'];
            }


            $annulerPayload = [
                'docstatus' => 2 
            ];

            $newPaypload = [
                'employee'=>$employee,
                'base' => $salary_reduit,
                'salary_structure' => $salary_structure,
                'from_date' => $from,
                'to_date' =>$to
            ];

            //cancel 
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
            
            
            if(!$createdRespons->successful())
            {
                Log::error("Échec de mise à jour SSA forcé : " . $createdRespons->body());
            }else {
                Log::info('Salary Salary Assignment cree avec success : ' . $employee, ['mois' => $from]);
            }


            $slipPayload = [
                'employee' => $employee,
                'salary_structure' => $salary_structure,
                'start_date' => $from,
                'end_date' => $to,
                'creation' => now()->toDateTimeString(),
                'docstatus' => 1
             ];

        //cancel slip
        $cancelResponce = Http::withHeaders( [
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'])
            ->put($this->baseUrl . '/api/resource/Salary Slip/' . $salarySlipName, ['data' => $annulerPayload]);
        
        //creation slip
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
                Log::info('Salary Slip cree avec succsse : ' . $employee, [ 'mois' => $from]);
                }
            
            }
        }

        $payload = [
            'docstatus' => 1,
            'employee' => $employee,
            'salary_structure' => $salary_structure,
            'base' => $salary_reduit,
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
                Log::info('Salary Slip cree avec succsse : ' . $employee, [ 'mois' => $from]);
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
