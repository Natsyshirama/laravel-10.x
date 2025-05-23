<?php

namespace App\Http\Controllers\QuotationClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\QuotationAPI;

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
}
