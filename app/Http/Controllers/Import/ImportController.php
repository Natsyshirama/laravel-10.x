<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;


class ImportController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FRAPPE_URL', 'http://erpnext.localhost:8000/');
    }
    public function index(){
        return view('import.import');
    }
    public function importEmployees(Request $request)
    {
        $sid = Session::get('sid');
        if (!$sid) {
            throw new \Exception('Non connecté');
        }
    
        $file = $request->file('csv_fileEmployee');
        $csvContent = file_get_contents($file->getRealPath());
        
        try {
            $response = Http::timeout(300) // Augmenter le timeout à 5 minutes
                ->withHeaders([
                    'Cookie' => 'sid=' . $sid,
                    'Content-Type' => 'application/json',
                ])->post($this->baseUrl . '/api/method/erpnext.importation.page.importdata.importEmployee.importEmployee', [
                    'data' => $csvContent,
                ]);
    
            return back()->with('status', $response->json());
    
        } catch (\Exception $e) {
            Log::error('Erreur lors import EMPLOYEE', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }
    
    

    public function importSalaryStructure(Request $request){
        $sid = Session::get('sid');
    
        if (!$sid) {
            throw new \Exception('Non connecté');
        }  
        $file = $request->file('csv_fileSalaryStructure');
        $csvContent = file_get_contents($file->getRealPath());
        try{
            $response = Http::withHeaders([
                'Cookie' => 'sid=' . $sid,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/api/method/erpnext.importation.page.importdata.importSalaryStructure.import_salary_structure', [
                'data' => $csvContent
            ]);
            return back()->with('status', $response->json());
    
    }catch (\Exception $e) {
        Log::error('erreur lors import data EMPLOYEE', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
    
    }

public function importSalarySlip(Request $request){
    $sid = Session::get('sid');

    if (!$sid) {
        throw new \Exception('Non connecté');
    }  
    $file = $request->file('csv_fileSalarySlip');
    $csvContent = file_get_contents($file->getRealPath());
    try{
        $response = Http::timeout(300)
        ->withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/method/erpnext.importation.page.importdata.importSalarySlip.import_salary_slip', [
            'data' => $csvContent
        ]);
        return back()->with('status', $response->json());

}catch (\Exception $e) {
    Log::error('erreur lors import data Salary slip', [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
    throw $e;
}


}

public function prepareImport(Request $request)
{
    $request->validate([
        'csv_employees' => 'required|file|mimes:csv,txt',
        'csv_salary_structure' => 'required|file|mimes:csv,txt',
        'csv_salary_slip' => 'required|file|mimes:csv,txt',
    ]);

    $datasets = [];

    foreach ([
        'csv_employees',
        'csv_salary_structure',
        'csv_salary_slip'
    ] as $field) {
        $file = $request->file($field);
        $rows = array_map('str_getcsv', file($file->getRealPath()));

        if (empty($rows)) {
            return back()->with('error', "Le fichier $field est vide.");
        }

        $header = array_shift($rows); 
        $datasets[$field] = [
            'headers' => $header,
            'rows' => $rows,
            'count' => count($rows),
        ];
    }

    session(['import_data' => $datasets]); 
    return view('import.fill_form', compact('datasets'));
}

public function confirmImport(Request $request)
{
    $sid = Session::get('sid');
    if (!$sid) {
        return back()->with('error', 'Non connecté');
    }

    $httpClient = Http::timeout(600)->withHeaders([
        'Cookie' => 'sid=' . $sid,
        'Content-Type' => 'application/json',
    ]);

    $datasets = session('import_data');
    if (!$datasets) {
        return back()->with('error', 'Aucune donnée à importer.');
    }

    $linesRequested = $request->input('lines');
    $results = [];
    $hasError = false;

    foreach ($datasets as $key => $dataset) {
        if (!isset($linesRequested[$key])) continue;

        $lineCount = (int) $linesRequested[$key];
        $rows = array_slice($dataset['rows'], 0, $lineCount);
        array_unshift($rows, $dataset['headers']); 

        $csvContent = implode("\n", array_map(fn($r) => implode(',', $r), $rows));

        $endpoint = match($key) {
            'csv_employees' => '/api/method/erpnext.importation.page.importdata.importEmployee.importEmployee',
            'csv_salary_structure' => '/api/method/erpnext.importation.page.importdata.importSalaryStructure.import_salary_structure',
            'csv_salary_slip' => '/api/method/erpnext.importation.page.importdata.importSalarySlip.import_salary_slip',
        };

        $response = $httpClient->post($this->baseUrl . $endpoint, [
            'data' => $csvContent
        ]);

        $results[$key] = $response->json();
        if ($response->failed()) $hasError = true;
    }

    session()->forget('import_data'); 
    return redirect()->route('import.index')
        ->with('results', $results)
        ->with('status', $hasError ? 'partial' : 'success');
}

public function importAll(Request $request)
{
    $sid = Session::get('sid');
    if (!$sid) {
        return back()->with('error', 'Non connecté');
    }

    $results = [];
    $hasError = false;

    try {
        if (!$request->hasFile('csv_employees') || !$request->hasFile('csv_salary_structure') || !$request->hasFile('csv_salary_slip')) {
            throw new \Exception('Tous les fichiers doivent être fournis');
        }

        $httpClient = Http::timeout(600) 
            ->withHeaders([
                'Cookie' => 'sid=' . $sid,
                'Content-Type' => 'application/json',
            ]);

        $employeeContent = file_get_contents($request->file('csv_employees')->getRealPath());
        $response = $httpClient->post($this->baseUrl.'/api/method/erpnext.importation.page.importdata.importEmployee.importEmployee', [
            'data' => $employeeContent
        ]);
        $results['employees'] = $response->json();
        if ($response->failed()) $hasError = true;

        $structureContent = file_get_contents($request->file('csv_salary_structure')->getRealPath());
        $response = $httpClient->post($this->baseUrl.'/api/method/erpnext.importation.page.importdata.importSalaryStructure.import_salary_structure', [
            'data' => $structureContent
        ]);
        $results['salary_structure'] = $response->json();
        if ($response->failed()) $hasError = true;

        $slipContent = file_get_contents($request->file('csv_salary_slip')->getRealPath());
        $response = $httpClient->post($this->baseUrl.'/api/method/erpnext.importation.page.importdata.importSalarySlip.import_salary_slip', [
            'data' => $slipContent
        ]);
        $results['salary_slip'] = $response->json();
        if ($response->failed()) $hasError = true;

        return back()
            ->with('results', $results)
            ->with('status', $hasError ? 'partial' : 'success');

    } catch (\Exception $e) {
        Log::error('Erreur import global', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()
            ->with('error', 'Erreur import global: '.$e->getMessage())
            ->with('results', $results ?? []);
    }
}

}