<?php

namespace App\Http\Controllers\Reduction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReductionService;

class ReductionController extends Controller
{
    protected $reduction;
    public function __construct(ReductionService $reduction)
    {
        $this->reduction = $reduction;
    }

    public function createForm(){
        return view('reduction.create');
    }

    public function insertReduction(Request $request){
        $reduction = $request->validate([
            'mois' => 'required|string|max:255',
            'valeur' => 'required|integer|min:0'
        ]);

        $insertReduction =  $this->reduction->insertReduction($reduction['mois'], $reduction['valeur']);

        return redirect()->route('reduction.create')->with('success', 'Réduction ajoutée avec succès');
    }
}
