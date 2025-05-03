<?php

namespace App\Http\Controllers\Devise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DevisSuppAPI;
use App\Services\FournisseurAPI;
use Illuminate\Support\Facades\Session;

class DevisController extends Controller
{
    protected $devisApi;
    protected $fournisseurApi;

    public function __construct(DevisSuppAPI $devisApi, FournisseurAPI $fournisseurApi)
    {
        $this->devisApi = $devisApi;
        $this->fournisseurApi = $fournisseurApi;
    }
    public function filtre()
    {
        try {
            $suppliers = $this->fournisseurApi->getAllFournisseurs();
            return view('devis.filtre', [
                'suppliers' => $suppliers
            ]);
        } catch (\Exception $e) {
            Session::forget('sid');
            return redirect()->route('login')->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function index(Request $request)
    {
        try {
            $selectedSupplier = $request->input('supplier');

            $suppliers = $this->fournisseurApi->getAllFournisseurs();

            $filters = [];
            if ($selectedSupplier) {
                $filters['filters'] = json_encode([['supplier', '=', $selectedSupplier]]);
            }

            $devis = $this->devisApi->getQuotations($filters);

            return view('devis.index', [
                'devis' => $devis,
                'suppliers' => $suppliers,
                'selectedSupplier' => $selectedSupplier
            ]);
        } catch (\Exception $e) {
            Session::forget('sid');
            return redirect()->route('login')->withErrors(['message' => $e->getMessage()]);
        }
    }
    public function show($name)
{
    try {
        $details = $this->devisApi->getQuotationDetails($name);

        return view('devis.show', [
            'devis' => $details
        ]);
    } catch (\Exception $e) {
        Session::forget('sid');
        return redirect()->route('login')->withErrors(['message' => $e->getMessage()]);
    }
}
}