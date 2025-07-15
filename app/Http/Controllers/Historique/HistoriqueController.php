<?php

namespace App\Http\Controllers\Historique;

use App\Http\Controllers\Controller;
use App\Services\HistoriqueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HistoriqueController extends Controller
{
    protected $historique;
    public function __construct(HistoriqueService $historique)
    {
        $this->historique = $historique;
    }
    public function getListeHisto(){
        try{
            $historiques = $this->historique->getHistorique();

            return view('historique.listeHistorique',[
                'historiques'=> $historiques
            ]);
        }catch (\Exception $e) {
            Log::error('Erreur lors de la récupération liste reduction', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);

        }
    }
}
