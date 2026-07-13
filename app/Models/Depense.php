<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    public const STATUTS = [
        'en_attente' => 'En attente',
        'valide' => 'Approuvé',
        'paye' => 'Payé',
    ];

    public const STATUT_APPROUVE = 'valide';

    public static function statutsForUser(?User $user = null): array
    {
        $statuts = self::STATUTS;

        if (! $user?->canApproveDepense()) {
            unset($statuts[self::STATUT_APPROUVE]);
        }

        return $statuts;
    }


    public const CATEGORIES = [
        'sponsoring_reseaux' => 'Boost Facebook',
        'production_contenu' => 'Production contenu',
        'impression' => 'Impression',
        'goodies_evenements' => 'Goodies / Événements',
    ];

    protected $fillable = [
        'fournisseur',
        'objet',
        'campagne',
        'montant',
        'statut',
        'categorie',
        'date_depense',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
    ];

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
