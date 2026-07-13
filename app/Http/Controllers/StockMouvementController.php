<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMouvementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMouvement::with('stock')->orderByDesc('date_mouvement')->orderByDesc('id');

        if ($request->filled('type') && in_array($request->type, ['entree', 'sortie'], true)) {
            $query->where('type', $request->type);
        }

        if ($request->filled('stock_id')) {
            $query->where('stock_id', $request->stock_id);
        }

        $mouvements = $query->paginate(20)->withQueryString();
        $stocks = Stock::orderBy('article')->get();

        return view('stocks.mouvements.index', compact('mouvements', 'stocks'));
    }

    public function create(Request $request)
    {
        return view('stocks.mouvements.create', [
            'stocks' => Stock::orderBy('article')->get(),
            'types' => StockMouvement::TYPES,
            'selectedStockId' => $request->get('stock_id'),
            'selectedType' => $request->get('type', 'sortie'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateMouvement($request);

        DB::transaction(function () use ($validated) {
            $stock = Stock::lockForUpdate()->findOrFail($validated['stock_id']);

            if ($validated['type'] === 'sortie' && $validated['quantite'] > $stock->quantite) {
                throw ValidationException::withMessages([
                    'quantite' => 'Stock insuffisant pour « '.$stock->article.' » (disponible : '.$stock->quantite.').',
                ]);
            }

            StockMouvement::create($validated);

            $stock->quantite = $validated['type'] === 'entree'
                ? $stock->quantite + $validated['quantite']
                : $stock->quantite - $validated['quantite'];

            $stock->save();
        });

        $label = $validated['type'] === 'entree' ? 'Entrée' : 'Sortie';

        return redirect()->route('stocks.mouvements.index')
            ->with('success', $label.' enregistrée. Le stock a été mis à jour automatiquement.');
    }

    public function edit(StockMouvement $mouvement)
    {
        return view('stocks.mouvements.edit', [
            'mouvement' => $mouvement->load('stock'),
            'stocks' => Stock::orderBy('article')->get(),
            'types' => StockMouvement::TYPES,
        ]);
    }

    public function update(Request $request, StockMouvement $mouvement)
    {
        $validated = $this->validateMouvement($request);

        DB::transaction(function () use ($validated, $mouvement) {
            // Reverse previous movement
            $oldStock = Stock::lockForUpdate()->findOrFail($mouvement->stock_id);
            $oldStock->quantite = $mouvement->type === 'entree'
                ? $oldStock->quantite - $mouvement->quantite
                : $oldStock->quantite + $mouvement->quantite;
            $oldStock->save();

            // Apply new movement
            $newStock = $validated['stock_id'] === $mouvement->stock_id
                ? $oldStock
                : Stock::lockForUpdate()->findOrFail($validated['stock_id']);

            if ($validated['type'] === 'sortie' && $validated['quantite'] > $newStock->quantite) {
                throw ValidationException::withMessages([
                    'quantite' => 'Stock insuffisant pour « '.$newStock->article.' » (disponible : '.$newStock->quantite.').',
                ]);
            }

            $mouvement->update($validated);

            $newStock->quantite = $validated['type'] === 'entree'
                ? $newStock->quantite + $validated['quantite']
                : $newStock->quantite - $validated['quantite'];

            $newStock->save();
        });

        return redirect()->route('stocks.mouvements.index')
            ->with('success', 'Mouvement mis à jour. Les stocks ont été recalculés.');
    }

    public function destroy(StockMouvement $mouvement)
    {
        DB::transaction(function () use ($mouvement) {
            $stock = Stock::lockForUpdate()->findOrFail($mouvement->stock_id);

            // Reverse the movement
            if ($mouvement->type === 'entree') {
                if ($mouvement->quantite > $stock->quantite) {
                    throw ValidationException::withMessages([
                        'mouvement' => 'Impossible de supprimer cette entrée : le stock actuel ('.$stock->quantite.') est inférieur à la quantité à annuler ('.$mouvement->quantite.').',
                    ]);
                }
                $stock->quantite -= $mouvement->quantite;
            } else {
                $stock->quantite += $mouvement->quantite;
            }

            $stock->save();
            $mouvement->delete();
        });

        return redirect()->route('stocks.mouvements.index')
            ->with('success', 'Mouvement supprimé. Le stock a été rétabli.');
    }

    private function validateMouvement(Request $request): array
    {
        return $request->validate([
            'stock_id' => ['required', 'exists:stocks,id'],
            'type' => ['required', Rule::in(array_keys(StockMouvement::TYPES))],
            'quantite' => ['required', 'integer', 'min:1'],
            'date_mouvement' => ['required', 'date'],
            'motif' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
