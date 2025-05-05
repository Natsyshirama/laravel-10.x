<?php

namespace App\Http\Controllers\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FactureAchatAPI;

class FactureAchatController extends Controller
{
    //
    protected $factureAchatApi;

    public function __construct(FactureAchatAPI $factureAchatApi)
    {
        $this->factureAchatApi = $factureAchatApi;
    }

    public function index(Request $request)
    {
        try {

            $selectType = $request->input('type');

            $filters = [];
            if ($selectType === 'payer') {
                $filters['filters'] = json_encode([['is_paid', '=', 1]]);
            }elseif($selectType === 'non_payer'){
                    $filters['filters'] = json_encode([['is_paid', '=',0]]);
                
            }elseif($selectType === 'enretard'){
                $filters['filters'] = json_encode([['is_paid', '=',0],['due_date', '<', date('Y-m-d')]]);
            }

            $factures = $this->factureAchatApi->getFacturesAchat($filters);

            return view('factures.achat.index', [
                'factures' => $factures,
                'selectType' => $selectType
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function show($name)
    {
        try {
            $facture = $this->factureAchatApi->getFacturesAchatDetails($name);

            return view('factures.achat.show', [
                'facture' => $facture,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }


    public function validateFacture($name)
{
    try {
        $this->factureAchatApi->validerFacture($name);
        return redirect()->back()->with('success', 'Facture validée avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}

    /**
     * Effectue le paiement d'une facture d'achat.
     *
     * @param string $factureName Le nom de la facture à payer.
     * @return \Illuminate\Http\RedirectResponse
     */
public function payFacture($factureName)
{
    try {
        // Vérifie que la facture existe et est valide
        $facture = $this->factureAchatApi->getFacturesAchatDetails($factureName);

        if ($facture['docstatus'] == 0) {
            return redirect()->back()->withErrors(['message' => "La facture est en brouillon. Veuillez la valider avant de la payer."]);
        }

        // Effectue le paiement et le valide
        $payment = $this->factureAchatApi->payFacture($factureName);

        return redirect()->route('factures.achat.index')->with('success', "Le paiement a été effectué avec succès pour la facture {$factureName}.");
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}

}
