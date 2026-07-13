<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['montant', 'annee', 'mois'];

    protected $casts = [
        'montant' => 'decimal:2',
    ];
}
