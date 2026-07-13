<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetAnnuel;
use App\Models\Campagne;
use App\Models\Depense;
use App\Models\Stock;
use App\Services\AlerteService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, AlerteService $alerteService)
    {
        $annee = (int) $request->get('annee', now()->year);
        $mois = (int) $request->get('mois', now()->month);

        $budgetMensuel = Budget::where('annee', $annee)->where('mois', $mois)->first();
        $budgetMontant = $budgetMensuel ? (float) $budgetMensuel->montant : 0;

        $budgetAnnuel = BudgetAnnuel::where('annee', $annee)->first();
        $budgetAnnuelMontant = $budgetAnnuel ? (float) $budgetAnnuel->montant : 0;
        $budgetAnnuelAlloue = (float) Budget::where('annee', $annee)->sum('montant');
        $budgetAnnuelRestant = max(0, $budgetAnnuelMontant - $budgetAnnuelAlloue);
        $pctAnnuelAlloue = $budgetAnnuelMontant > 0
            ? (int) min(100, round(($budgetAnnuelAlloue / $budgetAnnuelMontant) * 100))
            : 0;

        $depensesMois = Depense::whereYear('date_depense', $annee)
            ->whereMonth('date_depense', $mois)
            ->get();

        $totalDepense = (float) $depensesMois->sum('montant');
        $resteDisponible = max(0, $budgetMontant - $totalDepense);
        $sponsoringTotal = (float) $depensesMois->where('categorie', 'sponsoring_reseaux')->sum('montant');

        $pctUtilise = $budgetMontant > 0 ? round(($totalDepense / $budgetMontant) * 100) : 0;
        $pctRestant = $budgetMontant > 0 ? round(($resteDisponible / $budgetMontant) * 100) : 0;
        $pctSponsoring = $budgetMontant > 0 ? round(($sponsoringTotal / $budgetMontant) * 100) : 0;

        $nbOperations = $depensesMois->count();
        $nbCampagnesActives = Campagne::where('statut', 'active')->count();

        $kpis = [
            'budget_annuel' => $budgetAnnuelMontant,
            'budget_annuel_alloue' => $budgetAnnuelAlloue,
            'budget_annuel_restant' => $budgetAnnuelRestant,
            'pct_annuel_alloue' => $pctAnnuelAlloue,
            'budget_mensuel' => $budgetMontant,
            'depense' => $totalDepense,
            'reste' => $resteDisponible,
            'sponsoring' => $sponsoringTotal,
            'pct_utilise' => $pctUtilise,
            'pct_restant' => $pctRestant,
            'pct_sponsoring' => $pctSponsoring,
            'nb_operations' => $nbOperations,
            'nb_campagnes' => $nbCampagnesActives,
        ];

        $chartEvolution = $this->buildEvolutionChart($annee);
        $chartRepartition = $this->buildRepartitionChart($depensesMois, $totalDepense);

        $depensesRecentes = Depense::orderByDesc('date_depense')->limit(5)->get();
        $stocks = Stock::orderBy('article')->limit(5)->get();

        $alertes = $alerteService->all($annee, $mois);
        $nbAlertes = count($alertes);

        $moisLabel = Carbon::create($annee, $mois, 1)->locale('fr')->isoFormat('MMMM YYYY');

        return view('dashboard', compact(
            'kpis',
            'chartEvolution',
            'chartRepartition',
            'depensesRecentes',
            'stocks',
            'alertes',
            'nbAlertes',
            'moisLabel',
            'annee',
            'mois'
        ));
    }

    private function buildEvolutionChart(int $annee): array
    {
        $moisLabels = ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'];
        $budgetPrevu = [];
        $depenses = [];
        $reste = [];

        for ($m = 1; $m <= 12; $m++) {
            $budget = Budget::where('annee', $annee)->where('mois', $m)->first();
            $budgetVal = $budget ? (float) $budget->montant : 0;
            $depVal = (float) Depense::whereYear('date_depense', $annee)->whereMonth('date_depense', $m)->sum('montant');

            $budgetPrevu[] = $budgetVal;
            $depenses[] = $depVal;
            $reste[] = max(0, $budgetVal - $depVal);
        }

        return [
            'labels' => $moisLabels,
            'budget_prevu' => $budgetPrevu,
            'depenses' => $depenses,
            'reste' => $reste,
        ];
    }

    private function buildRepartitionChart($depensesMois, float $total): array
    {
        $categories = [
            'sponsoring_reseaux' => ['label' => 'Boost Facebook', 'color' => '#3b82f6'],
            'production_contenu' => ['label' => 'Production contenu', 'color' => '#8b5cf6'],
            'impression' => ['label' => 'Impression', 'color' => '#f97316'],
            'goodies_evenements' => ['label' => 'Goodies / Événements', 'color' => '#22c55e'],
        ];

        $data = [];
        foreach ($categories as $key => $meta) {
            $montant = (float) $depensesMois->where('categorie', $key)->sum('montant');
            $data[] = [
                'label' => $meta['label'],
                'montant' => $montant,
                'color' => $meta['color'],
                'pct' => $total > 0 ? round(($montant / $total) * 100) : 0,
            ];
        }

        return ['items' => $data, 'total' => $total];
    }
}
