<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Assignement;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    //
    protected $assign;

    public function __construct(Assignement $assign)
    {
        $this->assign = $assign;
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

}
