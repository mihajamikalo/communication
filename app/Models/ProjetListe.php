<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProjetListe extends Model
{
    protected $table = 'projet_listes';

    protected $fillable = [
        'projet_tableau_id',
        'nom',
        'slug',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function tableau(): BelongsTo
    {
        return $this->belongsTo(ProjetTableau::class, 'projet_tableau_id');
    }

    public function cartes(): HasMany
    {
        return $this->hasMany(ProjetCarte::class)->orderBy('position');
    }

    public static function makeSlug(string $nom): string
    {
        $base = Str::slug($nom, '_');
        if ($base === '') {
            $base = 'liste';
        }

        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'_'.$i++;
        }

        return $slug;
    }
}
