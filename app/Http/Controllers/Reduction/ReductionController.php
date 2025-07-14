<?php

namespace App\Http\Controllers\Reduction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReductionService;
use Illuminate\Support\Facades\Log;

class ReductionController extends Controller
{
    protected $reduction;
    public function __construct(ReductionService $reduction)
    {
        $this->reduction = $reduction;
    }

    //Liste Reductiom
    public function listeReduction(Request $request){

        try{
            $mois = $request->input('mois');
            $reductions = $this->reduction->getAllReduction($mois);
        
            return view('reduction.listeReduction',[
                'reductions'=> $reductions,
                'mois' => $mois
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération liste reduction', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    //**CREATION REDUCTION(Insert) */
    public function createForm(){
        return view('reduction.create');
    }

    public function insertReduction(Request $request){
        $reduction = $request->validate([
            'mois' => 'required|string|max:255',
            'valeur' => 'required|integer'
        ]);

        $insertReduction =  $this->reduction->insertReduction($reduction['mois'], $reduction['valeur']);

        return redirect()->route('reduction.create')->with('success', 'Réduction ajoutée avec succès');
    }


    //**MODIFICATION REDUCTION(update) */

    public function edit($id)
{
    $reduction = $this->reduction->getReductionById($id);
    return view('reduction.editReduction', ['reduction'=> $reduction]);
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'mois' => 'required|date',
        'valeur' => 'required|numeric',
    ]);

    try {
        $this->reduction->updateReduction($id, $validated);
        return redirect()->route('reduction.liste')->with('success', 'Réduction mise à jour avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur mise à jour réduction', ['error' => $e->getMessage()]);
        return redirect()->back()->withErrors(['message' => 'Erreur lors de la mise à jour.']);
    }
}

//**DELETE (id) */

    public function delete($id)
{
    try {
        $this->reduction->deleteReductionById($id);
        return redirect()->route('reduction.liste')->with('success', 'Réduction supprimée avec succès.');
    } catch (\Exception $e) {
        Log::error('Erreur suppression réduction', [
            'error' => $e->getMessage()
        ]);
        return redirect()->route('reduction.liste')->withErrors(['message' => 'Échec de la suppression.']);
    }
}

}
