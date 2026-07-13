<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EditorialEvent extends Model
{
    protected $fillable = [
        'titre',
        'categorie',
        'type_contenu',
        'booster',
        'date_debut',
        'date_fin',
        'statut',
        'valide',
        'texte_publication',
        'visuel_path',
        'visuel_nom',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'booster' => 'boolean',
        'valide' => 'boolean',
    ];

    public const CATEGORIES = [
        'facebook' => ['label' => 'Facebook', 'color_name' => 'Bleu', 'color' => '#1877F2', 'text' => '#ffffff'],
        'instagram' => ['label' => 'Instagram', 'color_name' => 'Rose', 'color' => '#E1306C', 'text' => '#ffffff'],
        'linkedin' => ['label' => 'LinkedIn', 'color_name' => 'Bleu foncé', 'color' => '#0A66C2', 'text' => '#ffffff'],
        'tiktok' => ['label' => 'Tik Tok', 'color_name' => 'Noir', 'color' => '#111827', 'text' => '#ffffff'],
        'befiana_sms' => ['label' => 'Befiana SMS', 'color_name' => 'Vert', 'color' => '#16a34a', 'text' => '#ffffff'],
        'brevo' => ['label' => 'Brevo', 'color_name' => 'Violet', 'color' => '#7c3aed', 'text' => '#ffffff'],
    ];

    public const TYPES_CONTENU = [
        'FI' => 'FI',
        'FP' => 'FP',
    ];

    public function getCategorieMetaAttribute(): array
    {
        return self::CATEGORIES[$this->categorie] ?? [
            'label' => $this->categorie,
            'color' => '#94a3b8',
            'text' => '#ffffff',
        ];
    }

    public function getIsFacebookFiAttribute(): bool
    {
        return $this->categorie === 'facebook' && $this->type_contenu === 'FI';
    }

    public function getVisuelUrlAttribute(): ?string
    {
        if (! $this->visuel_path) {
            return null;
        }

        return Storage::disk('public')->url($this->visuel_path);
    }

    protected static function booted(): void
    {
        static::deleting(function (EditorialEvent $event) {
            if ($event->visuel_path) {
                Storage::disk('public')->delete($event->visuel_path);
            }
        });
    }
}
