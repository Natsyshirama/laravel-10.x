<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Reduction\ReductionModel;

class ReductionService{
    public function getAllReduction(){
        return $reduction = ReductionModel::fromQuery("SELECT * FROM tabReduction");    }


    public function insertReduction(string $mois, int $valeur){
        return ReductionModel::create([
            
            'mois' => $mois,
            'valeur' => $valeur
        ]);
    }

    public function getReductionByMois(string $mois){
        return ReductionModel::fromQuery("SELECT valeur FROM tabReduction WHERE mois = ?", [$mois])->first();
    }
}