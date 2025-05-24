<?php

namespace App\Http\Controllers\QuotationClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuotationAPI;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\select;

class QuotationController extends Controller
{
  protected $quotaApi;
  public function __construct(QuotationAPI $quotaApi)
  {
    $this->quotaApi = $quotaApi;

  }

  public function index(Request $request){
    try{
            $selectStatus = $request->input('status');
            $filtre = [];
            if($selectStatus === 'ordered'){
                $filtre['filters'] = json_encode([['status', '=', 'Ordered']]);
            }elseif($selectStatus === 'open'){
                $filtre['filters'] = json_encode([['status', '=', 'Open' ]]);
            }elseif($selectStatus === 'draft'){
                $filtre['filters'] = json_encode([['status', '=', 'Draft']]);
            }

            $quotations = $this->quotaApi->getDevis($filtre);
            return view('devisClient.index', [
                'quotations' => $quotations,
                'selectStatus' => $selectStatus
            ]);

    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
  }

  public function show($name){
    try{
      $quotation = $this->quotaApi->getQuotationDetails($name);

      return view('devisClient.show', [
        'quotation' => $quotation,
      ]);
    }catch (\Exception $e) {
      return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
  }
  public function create(){
    try{
    $options = $this->quotaApi->getObjetSelection();

    return view('devisClient.creatForm',[
      'customers' => $options['customer'],
      'items' => $options['items']
    ]);
    }catch (\Exception $e) {
      return redirect()->back()->withErrors(['message' => $e->getMessage()]);
    }
  }
  public function store(Request $request)
  {
      $request->validate([
          'customer_name' => 'required|string',
          'transaction_date' => 'required|date',
          'items' => 'required|array|min:1',
          'items.*.item_code' => 'required|string',
          'items.*.quantity' => 'required|numeric|min:1', // Changé de qty à quantity
          'items.*.rate' => 'required|numeric|min:0'
      ]);
  
      try {
          $data = $request->all();
          $data['transaction_date'] = now()->format('Y-m-d');
          
          $quotation = $this->quotaApi->ajoutQuotationCustomer($data);
          
          if (!isset($quotation['name'])) {
              throw new \Exception('La création du devis a échoué: nom du devis non reçu');
          }
  
          return redirect()
              ->route('devisClient.show', $quotation['name'])
              ->with('success', 'Devis créé avec succès!');
  
      } catch (\Exception $e) {
          Log::error('Erreur création devis', ['error' => $e->getMessage()]);
          return back()
              ->withInput()
              ->with('error', $e->getMessage()); 
      }
  }
}
