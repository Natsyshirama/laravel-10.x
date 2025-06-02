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
    public function importSuppliers(Request $request)
    {
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }
        $file = $request->file('csv_file');
        $csvContent = file_get_contents($file->getRealPath());
    try{
        $response = Http::withHeaders([
            'Cookie' => 'sid=' . $sid,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/api/method/erpnext.importation.page.importdata.importData.import_csv', [
            'data' => $csvContent
        ]);
    
        return back()->with('status', $response->json());
    }catch (\Exception $e) {
        Log::error('erreur lors import data', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
    }

    public function importEmployees(Request $request){
        $sid = Session::get('sid');

        if (!$sid) {
            throw new \Exception('Non connecté');
        }  
        $file = $request->file('csv_fileEmployee');
        $csvContent = file_get_contents($file->getRealPath());
        try{
            $response = Http::withHeaders([
                'Cookie' => 'sid=' . $sid,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/api/method/erpnext.importation.page.importdata.importData.importEmployee', [
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
        ])->post($this->baseUrl . '/api/method/erpnext.importation.page.importdata.importData.import_salary_structure', [
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
}