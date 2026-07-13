<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetAnnuel extends Model
{
    protected $table = 'budget_annuels';

    protected $fillable = ['annee', 'montant'];

    protected $casts = [
        'montant' => 'decimal:2',
        'annee' => 'integer',
    ];

    public function budgetsMensuels()
    {
        return $this->hasMany(Budget::class, 'annee', 'annee');
    }

    public function montantAlloue(): float
    {
        return (float) Budget::where('annee', $this->annee)->sum('montant');
    }

    public function montantRestant(): float
    {
        return max(0, (float) $this->montant - $this->montantAlloue());
    }

    public function pourcentageAlloue(): int
    {
        if ((float) $this->montant <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->montantAlloue() / (float) $this->montant) * 100));
    }
}
