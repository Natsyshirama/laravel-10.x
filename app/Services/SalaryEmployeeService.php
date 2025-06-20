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

//component
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
        throw new \Exception('Non connecté');
    }

    $response = Http::withHeaders([
        'Cookie' => 'sid=' . $sid
        ])->post(env('FRAPPE_URL') . '/api/method/erpnext.mymodel.employee.get_employees_by_component', [
        'component_name' => $component,
        'operator' => $operator,
        'amount' => $amount,
    ]);

    if ($response->successful()) {
        return $response->json()['message'];    
    } else {
        throw new \Exception("Erreur API Frappe : " . $response->body());
    }
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
public function updateBaseSalaryForFilteredEmployees($component,$operator,$amount, $methode,$pourcentage)
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

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement de l\'employé', [
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



public function genereSalarySA($employee, $base_salary, $from_date, $to_date)
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
            Log::info("SSA déjà existant pour $employee au mois de $month, saut...");
            $current->addMonth();
            continue;
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
