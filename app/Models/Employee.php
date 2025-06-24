<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'tabEmployee';
    protected $fillable = [
        'name',
        'creation',
        'owner',
        'docstatus',
        'employee',
        'first_name',
        'middle_name',
        'last_name',
        'employee_name',
        'gender',
        'date_of_birth',
        'date_of_joining',
        'status',
        'company',
        'employee_number',
        'branch'

    ];
}
