<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Assignement;
use App\Services\SalaryEmployeeService;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    //
    protected $assign;
    protected $salaryStr;

    public function __construct(Assignement $assign,SalaryEmployeeService $salaryStr)
    {
        $this->assign = $assign;
        $this->salaryStr = $salaryStr;
    }

    public function addAssignment(){
        $employee = $this->assign->getEmployee();
        $salaryStr = $this->assign->getSalaryStr();
        try{
            return view('salary.addAssignment', [
                'employee' => $employee,
                'salaryStr' => $salaryStr
            ]);
        }catch (\Exception $e) {
            Log::error('Erreur lors de la récupération objet a selectionner', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function assignment(Request $request){
        $donner = $request->validate([
            'employee' => 'required|string',
            'employee_name' => 'string',
            'salary_structure' => 'required|string',
            'base' => 'nullable|numeric',
            'from_date' => 'required|date',
        ]);
        try{
            $this->assign->ajoutAssignement($donner);
            return redirect()->route('salaryAssg.addAssg')->with('success', 'Affectation enregistrée.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des listes Salary Structure', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
        
    }

    public function genereForm(){
        $employee = $this->assign->getEmployee();
        try{
            return view('genere.salaryGenerForm',[
                'employee' => $employee
            ]);
    }catch (\Exception $e) {
            Log::error('Erreur lors de la récupération objet a selectionner', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }

}

public function genereSSA(Request $request)
{
    $validated = $request->validate([
        'employee' => 'required|string',
        'base_salary' => 'nullable|numeric',
        'from_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:from_date',
    ]);

    try {
        $this->salaryStr->genereSalarySA(
            $validated['employee'],
            $validated['base_salary'],
            $validated['from_date'],
            $validated['end_date']
        );
        return redirect()->back()->with('success', 'SSA générés avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur lors generation SSA', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}


public function modifForm(){
    $components =  $this->salaryStr->getComponent();
    try{
        return view('modif.salaryModifForm', [
            'components' =>$components
        ]);
    }catch (\Exception $e) {
        Log::error('Erreur lors de la récupération objet a selectionner', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}
public function modifSalaire(Request $request)
{
    $validated = $request->validate([
        'component' => 'required|string',
        'signe' => 'required|in:>,<',
        'montant' => 'required|numeric',
        'methode' => 'required|in:plus,moins',
        'pourcentage' => 'required|numeric|min:0',
    ]);

    $component = $validated['component'];
    $operator = $validated['signe'];
    $amount = $validated['montant'];
    $methode = $validated['methode'];
    $pourcentage = $validated['pourcentage'];



    try {
        $nb = $this->salaryStr->updateBaseSalaryForFilteredEmployees($component,$operator,$amount, $methode,$pourcentage);
        return back()->with('success', "$nb employés mis à jour.");
    } catch (\Exception $e) {
        return back()->withErrors('Erreur : ' . $e->getMessage());
    }
}

}