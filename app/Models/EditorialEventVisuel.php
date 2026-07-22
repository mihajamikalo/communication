<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EditorialEventVisuel extends Model
{
    protected $fillable = [
        'editorial_event_id',
        'path',
        'nom',
        'position',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(EditorialEvent::class, 'editorial_event_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function toArrayPayload(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'nom' => $this->nom,
            'position' => $this->position,
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $visuel) {
            if ($visuel->path) {
                Storage::disk('public')->delete($visuel->path);
            }
        });
    }
}
