<?php

namespace App\Models\Historique;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueModel extends Model
{
    use HasFactory;

    protected $table = 'tabHistor';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'employee',
        'salaire',
        'dateAncien'
    ];
}
