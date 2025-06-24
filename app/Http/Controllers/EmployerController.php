<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmployeeAPI;

class EmployerController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeAPI $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('employess.index', compact('employees'));
    }

    public function show($id)
    {
        $employee = $this->employeeService->getEmployeeDetail($id);

        if (!$employee) {
            return redirect()->route('employees.index')->withErrors('Employé introuvable.');
        }

        return view('employess.show', compact('employee'));
    }
}
