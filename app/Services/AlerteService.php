<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetAnnuel;
use App\Models\Depense;
use App\Models\Evenement;
use App\Models\Stock;
use Carbon\Carbon;

class AlerteService
{
    public function all(?int $annee = null, ?int $mois = null): array
    {
        $annee = $annee ?? now()->year;
        $mois = $mois ?? now()->month;

        $snap = app(BudgetMensuelService::class)->forMonth($annee, $mois);
        $budgetMontant = $snap['budget_effectif'];
        $totalDepense = $snap['depense'];

        $budgetAnnuel = BudgetAnnuel::where('annee', $annee)->first();
        $budgetAnnuelMontant = $budgetAnnuel ? (float) $budgetAnnuel->montant : 0;
        $budgetAnnuelAlloue = (float) Budget::where('annee', $annee)->sum('montant');

        $alertes = [];
        $moisLabel = Carbon::create($annee, $mois, 1)->locale('fr')->isoFormat('MMMM YYYY');

        if ($snap['is_depassement']) {
            $pct = $snap['pct_utilise'];
            $alertes[] = [
                'type' => 'danger',
                'titre' => 'Dépassement budget mensuel',
                'description' => ucfirst($moisLabel).' : '.format_ar($totalDepense).' dépensés sur '.format_ar($budgetMontant).' effectifs ('.format_ar($snap['depassement']).' reportés au mois suivant, '.$pct.'%).',
                'temps' => 'Maintenant',
                'url' => route('depenses.index'),
            ];
        } elseif ($budgetMontant > 0 && $totalDepense >= $budgetMontant * 0.9) {
            $reste = max(0, $snap['reste']);
            $alertes[] = [
                'type' => 'warning',
                'titre' => 'Budget mensuel presque épuisé',
                'description' => 'Il reste '.format_ar($reste).' sur '.format_ar($budgetMontant).' en '.ucfirst($moisLabel).'.',
                'temps' => 'Maintenant',
                'url' => route('budgets.index'),
            ];
        } elseif ($budgetMontant <= 0) {
            $alertes[] = [
                'type' => 'warning',
                'titre' => 'Aucun budget mensuel',
                'description' => 'Aucun budget défini pour '.ucfirst($moisLabel).'.',
                'temps' => 'Maintenant',
                'url' => route('budgets.create'),
            ];
        }

        if ($budgetAnnuelMontant > 0) {
            $pctAlloue = ($budgetAnnuelAlloue / $budgetAnnuelMontant) * 100;
            $resteAnnuel = max(0, $budgetAnnuelMontant - $budgetAnnuelAlloue);

            if ($budgetAnnuelAlloue > $budgetAnnuelMontant) {
                $alertes[] = [
                    'type' => 'danger',
                    'titre' => 'Budget annuel dépassé',
                    'description' => format_ar($budgetAnnuelAlloue).' alloués en mensuel sur '.format_ar($budgetAnnuelMontant).' ('.$annee.').',
                    'temps' => 'Maintenant',
                    'url' => route('budget-annuels.index'),
                ];
            } elseif ($pctAlloue >= 90) {
                $alertes[] = [
                    'type' => 'warning',
                    'titre' => 'Budget annuel presque alloué',
                    'description' => 'Il reste '.format_ar($resteAnnuel).' à répartir sur '.$annee.' ('.round($pctAlloue).'% alloué).',
                    'temps' => 'Maintenant',
                    'url' => route('budget-annuels.index'),
                ];
            }
        }

        $stocksFaibles = Stock::whereColumn('quantite', '<=', 'seuil_alerte')
            ->orderBy('quantite')
            ->get();

        foreach ($stocksFaibles as $stock) {
            $type = $stock->quantite <= ($stock->seuil_alerte * 0.5) ? 'danger' : 'warning';
            $alertes[] = [
                'type' => $type,
                'titre' => 'Stock faible',
                'description' => $stock->article.' : '.$stock->quantite.' restant(s) (seuil '.$stock->seuil_alerte.').',
                'temps' => $stock->updated_at?->diffForHumans() ?? 'Récent',
                'url' => route('stocks.index'),
            ];
        }

        $depensesEnAttente = Depense::where('statut', 'en_attente')
            ->orderByDesc('date_depense')
            ->limit(5)
            ->get();

        foreach ($depensesEnAttente as $depense) {
            $alertes[] = [
                'type' => 'info',
                'titre' => 'Dépense en attente d\'approbation',
                'description' => $depense->objet.' — '.format_ar($depense->montant).' ('.$depense->fournisseur.').',
                'temps' => $depense->created_at?->diffForHumans() ?? $depense->date_depense->format('d/m/Y'),
                'url' => route('depenses.edit', $depense),
            ];
        }

        $evenementsProches = Evenement::where('statut', '!=', 'annule')
            ->whereDate('date_debut', '>=', now()->toDateString())
            ->whereDate('date_debut', '<=', now()->addDays(14)->toDateString())
            ->orderBy('date_debut')
            ->limit(3)
            ->get();

        foreach ($evenementsProches as $evenement) {
            $alertes[] = [
                'type' => 'info',
                'titre' => 'Événement à venir',
                'description' => $evenement->nom.' — '.$evenement->date_debut->locale('fr')->isoFormat('D MMM YYYY')
                    .($evenement->lieu ? ' · '.$evenement->lieu : ''),
                'temps' => $evenement->date_debut->diffForHumans(),
                'url' => route('evenements.edit', $evenement),
            ];
        }

        return $alertes;
    }

    public function count(?int $annee = null, ?int $mois = null): int
    {
        return count($this->all($annee, $mois));
    }
}
