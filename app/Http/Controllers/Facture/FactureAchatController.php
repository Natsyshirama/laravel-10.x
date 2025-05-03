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
}
