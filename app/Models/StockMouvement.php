<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMouvement extends Model
{
    public const TYPES = [
        'entree' => 'Entrée',
        'sortie' => 'Sortie',
    ];

    protected $fillable = [
        'stock_id',
        'type',
        'quantite',
        'date_mouvement',
        'motif',
        'reference',
        'notes',
    ];

    protected $casts = [
        'date_mouvement' => 'date',
        'quantite' => 'integer',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getIsEntreeAttribute(): bool
    {
        return $this->type === 'entree';
    }
}
