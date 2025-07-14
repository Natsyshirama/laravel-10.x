<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Reduction\ReductionModel;

class ReductionService{
    public function getAllReduction(?string $mois = null)
    {
        if ($mois) {
            return ReductionModel::fromQuery(
                "SELECT * FROM tabReduction WHERE DATE_FORMAT(mois, '%Y-%m') = ? ORDER BY mois DESC",
                [$mois]
            );
        }
    
        return ReductionModel::fromQuery("SELECT * FROM tabReduction ");
    }
    

    public function getReductionById(int $id)
    {
        return ReductionModel::findOrFail($id);
    }
    

    public function insertReduction(string $mois, int $valeur){
        return ReductionModel::create([
            
            'mois' => $mois,
            'valeur' => $valeur
        ]);
    }


    public function deleteReductionById(int $id): void
    {
        $reduction = ReductionModel::findOrFail($id);
        $reduction->delete();
    }
    
    public function updateReduction(int $id, array $data): void
{
    $reduction = ReductionModel::findOrFail($id);
    $reduction->mois = $data['mois'];
    $reduction->valeur = $data['valeur'];
    $reduction->save();
}

    public function getReductionMois(string $mois): ?float
    {
        $reduction = ReductionModel::where('mois', $mois)->first();
        return $reduction ? (float) $reduction->valeur : null;
    }
    
    
}