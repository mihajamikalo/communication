<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = ['article', 'quantite', 'seuil_alerte'];

    public function mouvements(): HasMany
    {
        return $this->hasMany(StockMouvement::class);
    }

    public function getStatutAttribute(): string
    {
        if ($this->quantite <= $this->seuil_alerte * 0.5) {
            return 'faible';
        }

        if ($this->quantite <= $this->seuil_alerte) {
            return 'moyen';
        }

        return 'bon';
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'faible' => 'Faible',
            'moyen' => 'Moyen',
            default => 'Bon',
        };
    }
}
