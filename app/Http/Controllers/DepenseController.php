<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Depense;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DepenseController extends Controller
{
    public function index()
    {
        $depenses = Depense::orderByDesc('date_depense')->paginate(15);

        return view('depenses.index', compact('depenses'));
    }

    public function create()
    {
        return view('depenses.create', [
            'fournisseurs' => Fournisseur::orderBy('nom')->pluck('nom'),
            'campagnes' => Campagne::orderBy('nom')->pluck('nom'),
            'statuts' => Depense::statutsForUser(auth()->user()),
            'categories' => Depense::CATEGORIES,
            'canApprove' => auth()->user()?->canApproveDepense() ?? false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateDepense($request);

        Depense::create($validated);

        return redirect()->route('depenses.index')->with('success', 'Dépense enregistrée avec succès.');
    }

    public function edit(Depense $depense)
    {
        return view('depenses.edit', [
            'depense' => $depense,
            'fournisseurs' => Fournisseur::orderBy('nom')->pluck('nom'),
            'campagnes' => Campagne::orderBy('nom')->pluck('nom'),
            'statuts' => Depense::statutsForUser(auth()->user()),
            'categories' => Depense::CATEGORIES,
            'canApprove' => auth()->user()?->canApproveDepense() ?? false,
        ]);
    }

    public function update(Request $request, Depense $depense)
    {
        $validated = $this->validateDepense($request, $depense);

        $depense->update($validated);

        return redirect()->route('depenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Depense $depense)
    {
        $depense->delete();

        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée avec succès.');
    }

    private function validateDepense(Request $request, ?Depense $depense = null): array
    {
        $user = auth()->user();
        $allowedStatuts = array_keys(Depense::statutsForUser($user));

        // Keep current approved status if a non-super-admin edits without changing it
        if ($depense && $depense->statut === Depense::STATUT_APPROUVE && ! $user?->canApproveDepense()) {
            $allowedStatuts[] = Depense::STATUT_APPROUVE;
        }

        $validated = $request->validate([
            'fournisseur' => ['required', 'string', 'max:255'],
            'objet' => ['required', 'string', 'max:255'],
            'campagne' => ['nullable', 'string', 'max:255'],
            'montant' => ['required', 'numeric', 'min:0'],
            'statut' => ['required', Rule::in($allowedStatuts)],
            'categorie' => ['required', Rule::in(array_keys(Depense::CATEGORIES))],
            'date_depense' => ['required', 'date'],
        ]);

        if (
            $validated['statut'] === Depense::STATUT_APPROUVE
            && ! $user?->canApproveDepense()
            && (! $depense || $depense->statut !== Depense::STATUT_APPROUVE)
        ) {
            throw ValidationException::withMessages([
                'statut' => 'Seul un Super Admin peut approuver une dépense.',
            ]);
        }

        // Non-super-admin cannot remove approval once set by changing away... 
        // Actually they shouldn't change FROM approved either without being super admin
        if (
            $depense
            && $depense->statut === Depense::STATUT_APPROUVE
            && $validated['statut'] !== Depense::STATUT_APPROUVE
            && ! $user?->canApproveDepense()
        ) {
            throw ValidationException::withMessages([
                'statut' => 'Seul un Super Admin peut modifier le statut Approuvé.',
            ]);
        }

        return $validated;
    }
}
