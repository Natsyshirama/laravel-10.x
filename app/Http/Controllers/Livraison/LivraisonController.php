<?php

namespace App\Http\Controllers\Livraison;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\LivraisonAPI;

use function Laravel\Prompts\select;

class LivraisonController extends Controller
{
    //
    protected $livraisonApi;

    public function __construct(LivraisonAPI $livraisonApi)
    {
        $this->livraisonApi = $livraisonApi;
    }
    public function index(Request $request){
        try{
            $selectStatus = $request->input('status');

            $filtre = [];
            if($selectStatus === 'a facturer'){
                $filtre['filters'] = json_encode([['status', '=', 'To Bill']]);
            }elseif($selectStatus === 'complet'){
                $filtre['filters'] =json_encode([['status', '=', 'Completed']]);
            }
            $livraions = $this->livraisonApi->getAllLivraison($filtre);

            return view('livraison.index', [
                'livraions' => $livraions,
                'selectStatus' => $selectStatus
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }

  public function show($name){
    try{
      $livraison = $this->livraisonApi->getLivraisonDetails($name);

      return view('livraison.show', [
        'livraison' => $livraison,
      ]);
    }catch (\Exception $e) {
      return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
  }
}
