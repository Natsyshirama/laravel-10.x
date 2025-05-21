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
    public function filtre()
    {
        try {
            $suppliers = $this->fournisseurApi->getAllFournisseurs();
            return view('commandes.filtre', [
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
            $selectedType = $request->input('type'); // 'facture' ou 'recu'
    
            $filters = [];
    
            if ($selectedType === 'paye') {
                $filters['filters'] = json_encode([['per_billed', '=', 100]]);
            } elseif ($selectedType === 'recu') {
                $filters['filters'] = json_encode([['per_received', '=', 100]]);
            } elseif ($selectedType === 'non_recu') {
                $filters['filters'] = json_encode([['per_received', '<', 100]]);
            }elseif ($selectedType === 'non_paye') {
                $filters['filters'] = json_encode([['per_billed', '<', 100]]);
            } elseif ($selectedType === 'paye_recu') {
                $filters['filters'] = json_encode([
                    ['per_billed', '=', 100],
                    ['per_received', '=', 100]
                ]);
            }
            $commandes = $this->commandeApi->getCommandesAchat($filters);
    
            return view('commandes.index', [
                'commandes' => $commandes,
                'selectedType' => $selectedType
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