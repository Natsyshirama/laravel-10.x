<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\Historique\HistoriqueModel;


class HistoriqueService{

    public function insertHistorique(string $employee, float $salaire, string $dateAncien ){
        return HistoriqueModel::create([
            'employee' =>$employee,
            'salaire' =>$salaire,
            'dateAncien' => $dateAncien
           
        ]);
}
public function getHistorique(){
    return HistoriqueModel::fromQuery("SELECT * FROM tabHistor ");

}
}