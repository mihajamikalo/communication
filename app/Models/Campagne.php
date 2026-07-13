<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campagne extends Model
{
    public const STATUTS = [
        'active' => 'Active',
        'terminee' => 'Terminée',
        'planifiee' => 'Planifiée',
    ];

    protected $fillable = [
        'nom',
        'objectif',
        'budget',
        'date_debut',
        'date_fin',
        'statut',
        'depense_id',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function depense(): BelongsTo
    {
        return $this->belongsTo(Depense::class);
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
