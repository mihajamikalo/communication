<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjetChecklist extends Model
{
    protected $table = 'projet_checklists';

    protected $fillable = ['projet_carte_id', 'titre', 'position'];

    public function carte(): BelongsTo
    {
        return $this->belongsTo(ProjetCarte::class, 'projet_carte_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjetChecklistItem::class)->orderBy('position');
    }
}
