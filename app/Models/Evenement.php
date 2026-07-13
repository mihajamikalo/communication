<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evenement extends Model
{
    public const TYPES = [
        'salon_etudiant' => 'Salon étudiant',
        'sortie_promotion' => 'Sortie de promotion',
        'portes_ouvertes' => 'Portes ouvertes',
        'conference' => 'Conférence / Webinaire',
        'forum' => 'Forum / Job dating',
        'autre' => 'Autre',
    ];

    public const STATUTS = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
    ];

    protected $fillable = [
        'nom',
        'type',
        'date_debut',
        'date_fin',
        'lieu',
        'cout',
        'statut',
        'description',
        'depense_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout' => 'decimal:2',
    ];

    public function depense(): BelongsTo
    {
        return $this->belongsTo(Depense::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
