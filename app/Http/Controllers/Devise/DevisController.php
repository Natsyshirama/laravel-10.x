<?php

namespace App\Http\Controllers\Devise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DevisSuppAPI;
use App\Services\FournisseurAPI;
use App\Services\ArticleAPI;
use Illuminate\Support\Facades\Session;

class DevisController extends Controller
{
    protected $devisApi;
    protected $fournisseurApi;
    protected $itemApi;
    public function __construct(DevisSuppAPI $devisApi, FournisseurAPI $fournisseurApi, ArticleAPI $itemApi)
{
    $this->devisApi = $devisApi;
    $this->fournisseurApi = $fournisseurApi;
    $this->itemApi = $itemApi;
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
        $itemsList = $this->itemApi->getAllItems();

        return view('devis.show', [
            'devis' => $details,
            'itemsList' => $itemsList,
        ]);
    } catch (\Exception $e) {
        Session::forget('sid');
        return redirect()->route('login')->withErrors(['message' => $e->getMessage()]);
    }
}


public function updateItem(Request $request, $name)
{
    try {
        $updatedData = $request->only(['item_code', 'description', 'qty', 'rate', 'uom']);
        $updatedData['item_code_originale'] = $request->input('item_code_originale'); 
        $this->devisApi->updateItemDetails($name, $updatedData);

        return redirect()->back()->with('success', 'Item mis à jour avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}

}