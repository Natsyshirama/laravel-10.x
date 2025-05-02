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
public function update(Request $request, $name)
{
    try {
        $original = $this->devisApi->getQuotationDetails($name);
        $docstatus = $original['docstatus'];

        if ($docstatus == 0) {
            // Cas 1: Le devis est un brouillon
            $newName = $name;
            
            // Mise à jour des items
            $items = $this->prepareItems($request->input('items'));
            $this->devisApi->updateQuotationItems($newName, $items);
            
            // Soumission du devis
            $this->devisApi->submitQuotation($newName);
            
        } elseif ($docstatus == 1) {
            // Cas 2: Le devis est soumis - NOUVEAU WORKFLOW
            // 1. Annuler d'abord l'original
            $this->devisApi->cancelQuotation($name);
            
            // 2. Créer le nouveau devis (clone)
            $newName = $this->devisApi->cloneQuotation($name);
            
            // 3. Mettre à jour les items
            $items = $this->prepareItems($request->input('items'));
            $this->devisApi->updateQuotationItems($newName, $items);
            
            // 4. Soumettre le nouveau devis
            $this->devisApi->submitQuotation($newName);
            
        } elseif ($docstatus == 2) {
            throw new \Exception("Le devis '$name' a été annulé et ne peut pas être modifié.");
        } else {
            throw new \Exception("Statut docstatus inconnu : $docstatus");
        }

        return redirect()->route('devis.show', ['name' => $newName])
            ->with('success', "Devis modifié avec succès.");

    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['message' => $e->getMessage()]);
    }
}

protected function prepareItems($inputItems)
{
    $items = [];
    foreach ($inputItems as $item) {
        $items[] = [
            'item_code' => $item['item_code'],
            'item_name' => $item['item_name'],
            'description' => $item['description'],
            'qty' => (float) $item['qty'],
            'rate' => (float) $item['rate'],
            'amount' => (float) $item['qty'] * (float) $item['rate'],
            'uom' => $item['uom']
        ];
    }
    return $items;
}

}