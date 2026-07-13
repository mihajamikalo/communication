<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Depense;
use App\Models\Evenement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EvenementController extends Controller
{
    public function index()
    {
        $evenements = Evenement::with('depense')
            ->orderByDesc('date_debut')
            ->paginate(15);

        return view('evenements.index', compact('evenements'));
    }

    public function create()
    {
        return view('evenements.create', [
            'types' => Evenement::TYPES,
            'statuts' => Evenement::STATUTS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvenement($request);

        $this->assertMonthlyBudgetAvailable(
            Carbon::parse($validated['date_debut']),
            (float) $validated['cout']
        );

        DB::transaction(function () use ($validated) {
            $depense = null;

            if ((float) $validated['cout'] > 0) {
                $depense = Depense::create($this->depensePayload($validated));
            }

            Evenement::create([
                ...$validated,
                'depense_id' => $depense?->id,
            ]);
        });

        return redirect()->route('evenements.index')
            ->with('success', 'Événement créé. Le coût a été déduit du budget mensuel.');
    }

    public function edit(Evenement $evenement)
    {
        return view('evenements.edit', [
            'evenement' => $evenement,
            'types' => Evenement::TYPES,
            'statuts' => Evenement::STATUTS,
        ]);
    }

    public function update(Request $request, Evenement $evenement)
    {
        $validated = $this->validateEvenement($request);

        $this->assertMonthlyBudgetAvailable(
            Carbon::parse($validated['date_debut']),
            (float) $validated['cout'],
            $evenement->depense_id
        );

        DB::transaction(function () use ($validated, $evenement) {
            if ((float) $validated['cout'] > 0) {
                $payload = $this->depensePayload($validated);

                if ($evenement->depense_id && $evenement->depense) {
                    $evenement->depense->update($payload);
                } else {
                    $depense = Depense::create($payload);
                    $validated['depense_id'] = $depense->id;
                }
            } elseif ($evenement->depense) {
                $evenement->depense->delete();
                $validated['depense_id'] = null;
            }

            $evenement->update($validated);
        });

        return redirect()->route('evenements.index')
            ->with('success', 'Événement mis à jour. Le budget mensuel a été ajusté.');
    }

    public function destroy(Evenement $evenement)
    {
        DB::transaction(function () use ($evenement) {
            $depense = $evenement->depense;
            $evenement->delete();
            $depense?->delete();
        });

        return redirect()->route('evenements.index')
            ->with('success', 'Événement supprimé. Le montant a été rétabli sur le budget mensuel.');
    }

    private function validateEvenement(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Evenement::TYPES))],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'cout' => ['required', 'numeric', 'min:0'],
            'statut' => ['required', Rule::in(array_keys(Evenement::STATUTS))],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function depensePayload(array $validated): array
    {
        return [
            'fournisseur' => 'Événement ESCM',
            'objet' => $validated['nom'].' ('.(Evenement::TYPES[$validated['type']] ?? $validated['type']).')',
            'campagne' => null,
            'montant' => $validated['cout'],
            'statut' => 'en_attente',
            'categorie' => 'goodies_evenements',
            'date_depense' => $validated['date_debut'],
        ];
    }

    private function assertMonthlyBudgetAvailable(Carbon $date, float $cout, ?int $ignoreDepenseId = null): void
    {
        if ($cout <= 0) {
            return;
        }

        $annee = $date->year;
        $mois = $date->month;

        $budget = Budget::where('annee', $annee)->where('mois', $mois)->first();
        $budgetMontant = $budget ? (float) $budget->montant : 0;

        if ($budgetMontant <= 0) {
            throw ValidationException::withMessages([
                'cout' => 'Aucun budget mensuel défini pour '.$date->locale('fr')->isoFormat('MMMM YYYY').'.',
            ]);
        }

        $query = Depense::whereYear('date_depense', $annee)->whereMonth('date_depense', $mois);
        if ($ignoreDepenseId) {
            $query->where('id', '!=', $ignoreDepenseId);
        }

        $dejaDepense = (float) $query->sum('montant');
        $reste = $budgetMontant - $dejaDepense;

        if ($cout > $reste + 0.009) {
            throw ValidationException::withMessages([
                'cout' => 'Dépasse le budget mensuel restant ('.format_ar(max(0, $reste)).' sur '.format_ar($budgetMontant).' en '.$date->locale('fr')->isoFormat('MMMM YYYY').').',
            ]);
        }
    }
}
