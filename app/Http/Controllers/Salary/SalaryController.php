<?php

namespace App\Http\Controllers\Salary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\SalaireAPI;
use App\Services\SalarySlipService;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaryController extends Controller
{
    protected $salaryApi;
    protected $salaire;
    public function __construct(SalaireAPI $salaryApi, SalarySlipService $salaire)
    {
        $this->salaire = $salaire;
    
        $this->salaryApi = $salaryApi;
    }


    public function index(Request $request)
    {
        $name = $request->input('name');
        if (!$name) {
            return redirect()->back()->withErrors(['message' => 'Le nom de l\'employé est requis']);
        }
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

    public function show(Request $request){
        $name = $request->input('name');
        try{
            $fichePaie = $this->salaryApi->getFichePaieDetails($name);
            return view('salary.fichePaieDetails', [
                'fichePaie' => $fichePaie
            ]);
        }catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des listes de fiche de paie', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function exportPdf( Request $request)
{
    $name = $request->input('name');
    if (!$name) {
        return redirect()->back()->withErrors(['message' => 'Le nom de fiche de paie est requis']);
    }
    try {
        $fichePaie = $this->salaryApi->getFichePaieDetails($name);

        $pdf = Pdf::loadView('salary.exportPdf', [
            'fichePaie' => $fichePaie
        ]);

        return $pdf->download('FichePaie_'.'_'.date('YmdHis').'.pdf');
        
    } catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des listes de fiche de paie', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}
public function filtreParMois(Request $request)
{
    $mois = $request->input('mois'); // peut être vide ou non

    try {
        $data = $this->salaryApi->getSalaryByMonth($mois);
        return view('salary.tableauSalaire', $data);
    } catch (\Exception $e) {
        return back()->withErrors(['message' => $e->getMessage()]);
    }
}
public function resumeParAnnee(Request $request)
{
    $annee = $request->input('annee', date('Y')); // par défaut année en cours

    try {
        $data = $this->salaire->getSalarySummaryByYear($annee);
        return view('salary.resumeAnnee', $data);
    } catch (\Exception $e) {
        return back()->withErrors(['message' => $e->getMessage()]);
    }
}

}
