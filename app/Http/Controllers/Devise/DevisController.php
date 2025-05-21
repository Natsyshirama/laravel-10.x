<?php

namespace App\Http\Controllers\Devise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DevisSuppAPI;
use App\Services\FournisseurAPI;
use App\Services\ArticleAPI;
use App\Services\SupplierQuotationService;
use Illuminate\Support\Facades\Session;

class DevisController extends Controller
{
    protected $devisApi;
    protected $fournisseurApi;
    protected $itemApi;
    protected $newService;

    public function __construct(SupplierQuotationService $newService,DevisSuppAPI $devisApi, FournisseurAPI $fournisseurApi, ArticleAPI $itemApi)
{
    $this->newService = $newService;
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
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
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
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
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
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}

public function createFormulaire()
{
    try {
        $suppliers = $this->newService->getAllFournisseurs();
        $items = $this->newService->getAllItem();
        $warehouses = $this->newService->getAllWarehouse();
        
        return view('devis.addDevis', [
            'suppliers' => $suppliers,
            'items' => $items,
            'warehouses' => $warehouses
        ]);
    } catch (\Exception $e) {
        return redirect()->route('devis.index')
               ->withErrors(['message' => 'Erreur de récupération des données: ' . $e->getMessage()]);
    }
}

public function updateAndSubmit(Request $request, $name)
{
    try {
        $items = $request->input('items');
        $this->devisApi->updateAndSubmitItems($name, $items);
        
        return redirect()->back()->with('success', 'Devis mis à jour et soumis avec succès!');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
}


public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'supplier' => 'required',
            'transaction_date' => 'required|date',
            'valid_till' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        $response = $this->newService->createSupplierQuotation($request->all());

        return redirect()->route('devis.show', $response['data']['name'])
               ->with('success', 'Devis créé avec succès!');

    } catch (\Exception $e) {
        return redirect()->back()
               ->withInput()
               ->withErrors(['message' => $e->getMessage()]);
    }
}

}