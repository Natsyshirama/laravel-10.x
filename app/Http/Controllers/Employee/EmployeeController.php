<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\EmployeeAPI;
use App\Services\SalaireAPI;


use Illuminate\Support\Facades\Log;
class EmployeeController extends Controller
{
    //

    protected $employeeApi;
    protected $salaryApi;
    public function __construct(EmployeeAPI $employeeApi, SalaireAPI $salaryApi)
    {
        $this->employeeApi = $employeeApi;
        $this->salaryApi = $salaryApi;
    }


    public function index(Request $request)
    {
        
        try {
            $departments = $this->employeeApi->getDepartement();
            $filtre = [];
            $selectDepartment = $request->input('department');
            if ($selectDepartment) {
                $filtre['filters'] = json_encode([['department', '=', $selectDepartment]]);
            }
            $employees = $this->employeeApi->getEmployees($filtre);
            return view('employee.index', [
                'departments' => $departments,
                'employees' => $employees,
                'selectDepartment' => $selectDepartment
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des employés', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }   
    
    public function show($name){
        try {
            $employee = $this->employeeApi->getEmployeeDetails($name);
            $salary = $this->salaryApi->getInfoSalaryEmployee($name);
            return view('employee.show', [
                'employee' => $employee,
                'salary' => $salary
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des détails de l\'employé', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
