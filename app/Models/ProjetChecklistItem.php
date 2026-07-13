<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjetChecklistItem extends Model
{
    protected $table = 'projet_checklist_items';

    protected $fillable = ['projet_checklist_id', 'titre', 'fait', 'position'];

    protected $casts = [
        'fait' => 'boolean',
        'position' => 'integer',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ProjetChecklist::class, 'projet_checklist_id');
    }
}
