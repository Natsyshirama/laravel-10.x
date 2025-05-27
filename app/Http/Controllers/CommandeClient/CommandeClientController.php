<?php

namespace App\Http\Controllers\CommandeClient;

use App\Http\Controllers\Controller;
use App\Services\CommandeAchatAPI;
use Illuminate\Http\Request;
use App\Services\CommandeClientAPI;
use function Laravel\Prompts\select;

class CommandeClientController extends Controller
{

    protected $commandApi;

    public function __construct(CommandeClientAPI $commandApi)
    {
        $this->commandApi = $commandApi;
    }

    public function index(Request $request){
        try{
            $selectStatus = $request->input('status');

            $filtre = [];
            if($selectStatus === 'facturee'){
                $filtre['filters'] = json_encode([['status', '=', 'To Bill']]);
            }elseif($selectStatus === 'livree'){
                $filtre['filters'] = json_encode([['status', '=', 'To Deliver']]);
            }elseif($selectStatus === 'livree et facturee'){
                $filtre['filters'] =json_encode([['status', '=', 'Completed']]);
            }
            $commandeClients = $this->commandApi->getAllCommande($filtre);

            return view('commandeClient.index', [
                'commandeClients' => $commandeClients,
                'selectStatus' => $selectStatus
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }


  public function show($name){
    try{
      $commande = $this->commandApi->getCommandeClientDetails($name);

      return view('commandeClient.show', [
        'commande' => $commande,
      ]);
    }catch (\Exception $e) {
      return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
  }

  public function validerCommande($name){
    try{
      $this->commandApi->validerCommande($name);
      return redirect()->back()->with('success', 'Devis validé avec succès.');

    }catch (\Exception $e) {
      return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
  }
}
