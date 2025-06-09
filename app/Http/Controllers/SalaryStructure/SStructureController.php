<?php

namespace App\Http\Controllers\SalaryStructure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SalaryStructur;

class SStructureController extends Controller
{
    protected $sstructApi;

    public function __construct(SalaryStructur $sstructApi)
    {
        $this->sstructApi = $sstructApi;
    }

    public function index(){
        try{
            $sstructures = $this->sstructApi->getSalaryStructure();

            return view('salaryStructure.index',[
                'sstructures' => $sstructures
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des listes Salary Structure', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function show(Request $request){
        $name = $request->input('name');
        try{
            $detail = $this->sstructApi->getSalaryStrDetails($name);
            return view('salaryStructure.show',
            [
                'detail' => $detail
            ]);

         }catch (\Exception $e) {
            Log::error('Erreur lors de la récupération details ', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function addForm(){
        $options = $this->sstructApi->getObjetSelection();
        try{
            return view('salaryStructure.createForm',[
                'company' => $options['company'],
                'gain' => $options['gain'],
                'deduction' => $options['deduction'],
            ]);
        }catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
          }
    }

    public function store(Request $request){
      $donner =  $request->validate([
            'company' => 'required|string',
            'name' => 'required|string',
            'earnings' => 'array', 
            'deductions' => 'array', 
        ]);

        try {
            $this->sstructApi->ajoutStructure($donner);
            return redirect()->route('salaraStr.addForm')->with('success', 'Structure enregistrée.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des listes Salary Structure', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

}
