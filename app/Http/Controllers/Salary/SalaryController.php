<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SalaireAPI;
class SalaryController extends Controller
{
    protected $salaryApi;
    public function __construct(SalaireAPI $salaryApi)
    {
        $this->salaryApi = $salaryApi;
    }
    public function index($name)
    {
        try {
            
            $slips = $this->salaryApi->getListeFichePaieEmployee($name);
            return view('salary.fichePaie', [
                'slips' => $slips
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des listes de fiche de paie', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
