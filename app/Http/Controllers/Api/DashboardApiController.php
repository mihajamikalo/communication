<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetAnnuel;
use App\Models\Campagne;
use App\Models\Depense;
use App\Services\AlerteService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function __invoke(Request $request, AlerteService $alerteService)
    {
        $annee = (int) $request->get('annee', now()->year);
        $mois = (int) $request->get('mois', now()->month);

        $budgetMensuel = Budget::where('annee', $annee)->where('mois', $mois)->first();
        $budgetMontant = $budgetMensuel ? (float) $budgetMensuel->montant : 0;

        $budgetAnnuel = BudgetAnnuel::where('annee', $annee)->first();
        $budgetAnnuelMontant = $budgetAnnuel ? (float) $budgetAnnuel->montant : 0;
        $budgetAnnuelAlloue = (float) Budget::where('annee', $annee)->sum('montant');

        $depensesMois = Depense::whereYear('date_depense', $annee)
            ->whereMonth('date_depense', $mois)
            ->get();

        $totalDepense = (float) $depensesMois->sum('montant');
        $resteDisponible = max(0, $budgetMontant - $totalDepense);
        $pctUtilise = $budgetMontant > 0 ? round(($totalDepense / $budgetMontant) * 100) : 0;

        $alertes = $alerteService->all($annee, $mois);

        return response()->json([
            'periode' => [
                'annee' => $annee,
                'mois' => $mois,
                'label' => Carbon::create($annee, $mois, 1)->locale('fr')->isoFormat('MMMM YYYY'),
            ],
            'kpis' => [
                'budget_annuel' => $budgetAnnuelMontant,
                'budget_annuel_alloue' => $budgetAnnuelAlloue,
                'budget_mensuel' => $budgetMontant,
                'depense' => $totalDepense,
                'reste' => $resteDisponible,
                'pct_utilise' => $pctUtilise,
                'nb_operations' => $depensesMois->count(),
                'nb_campagnes' => Campagne::where('statut', 'active')->count(),
                'nb_alertes' => count($alertes),
            ],
            'alertes' => collect($alertes)->take(8)->values(),
        ]);
    }
}
