<?php
namespace App\Http\Controllers\Commande;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommandeAchatAPI;
use App\Services\FournisseurAPI;
use Illuminate\Support\Facades\Session;

class CommandeController extends Controller
{
    protected $commandeApi;
    protected $fournisseurApi;

    public function __construct(CommandeAchatAPI $commandeApi, FournisseurAPI $fournisseurApi)
    {
        $this->commandeApi = $commandeApi;
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

            $commandes = $this->commandeApi->getCommandesAchat($filters);

            return view('commandes.index', [
                'commandes' => $commandes,
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
            $details = $this->commandeApi->getCommandeAchatDetails($name);

            return view('commandes.show', [
                'commande' => $details
            ]);
        } catch (\Exception $e) {
            Session::forget('sid');
            return redirect()->route('login')->withErrors(['message' => $e->getMessage()]);
        }
    }
}