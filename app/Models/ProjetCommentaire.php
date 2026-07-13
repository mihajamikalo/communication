<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjetCommentaire extends Model
{
    protected $table = 'projet_commentaires';

    protected $fillable = ['projet_carte_id', 'user_id', 'contenu'];

    public function carte(): BelongsTo
    {
        return $this->belongsTo(ProjetCarte::class, 'projet_carte_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
