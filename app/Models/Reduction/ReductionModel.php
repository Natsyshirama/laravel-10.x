<?php

namespace App\Models\Reduction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReductionModel extends Model
{
    use HasFactory;

    protected $table = 'tabReduction';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'mois',
        'valeur'
    ];
}
