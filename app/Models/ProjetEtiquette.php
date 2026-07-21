<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjetEtiquette extends Model
{
    protected $table = 'projet_etiquettes';

    public const COULEURS = [
        'yellow' => ['bg' => 'bg-yellow-400', 'text' => 'text-yellow-900', 'badge' => 'bg-yellow-100 text-yellow-800'],
        'blue' => ['bg' => 'bg-blue-500', 'text' => 'text-white', 'badge' => 'bg-blue-100 text-blue-800'],
        'red' => ['bg' => 'bg-red-500', 'text' => 'text-white', 'badge' => 'bg-red-100 text-red-800'],
        'green' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'badge' => 'bg-emerald-100 text-emerald-800'],
        'cyan' => ['bg' => 'bg-cyan-400', 'text' => 'text-cyan-900', 'badge' => 'bg-cyan-100 text-cyan-800'],
        'purple' => ['bg' => 'bg-purple-500', 'text' => 'text-white', 'badge' => 'bg-purple-100 text-purple-800'],
    ];

    protected $fillable = ['nom', 'couleur'];

    public function cartes(): BelongsToMany
    {
        return $this->belongsToMany(ProjetCarte::class, 'projet_carte_etiquette');
    }

    /**
     * Labels kept by default (Eisenhower matrix + Terminé).
     */
    public const DEFAULTS = [
        ['nom' => 'Urgent & Important', 'couleur' => 'red'],
        ['nom' => 'Urgent mais pas important', 'couleur' => 'yellow'],
        ['nom' => 'Ni important ni urgent', 'couleur' => 'purple'],
        ['nom' => 'Terminé', 'couleur' => 'green'],
    ];

    /** Labels to remove from existing databases. */
    public const REMOVED_NOMS = [
        'Communication',
        'Important',
        'Partenariat',
        'Pas urgent',
        'À traiter',
        'A traiter',
        'Événement',
        'Événements',
        'Evenement',
        'Evenements',
    ];

    public function getClassesAttribute(): array
    {
        return self::COULEURS[$this->couleur] ?? self::COULEURS['yellow'];
    }

    /**
     * Ensure default labels exist and remove deprecated ones.
     */
    public static function ensureDefaults(): void
    {
        static::whereIn('nom', self::REMOVED_NOMS)->each(function (self $etiquette) {
            $etiquette->cartes()->detach();
            $etiquette->delete();
        });

        foreach (self::DEFAULTS as $etiquette) {
            static::firstOrCreate(
                ['nom' => $etiquette['nom']],
                ['couleur' => $etiquette['couleur']]
            );
        }
    }

    public function toBoardArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'couleur' => $this->couleur,
            'classes' => $this->classes,
        ];
    }
}
