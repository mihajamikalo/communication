<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::orderBy('article')->paginate(15);

        return view('stocks.index', compact('stocks'));
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article' => ['required', 'string', 'max:255'],
            'quantite' => ['required', 'integer', 'min:0'],
            'seuil_alerte' => ['required', 'integer', 'min:1'],
        ]);

        Stock::create($validated);

        return redirect()->route('stocks.index')->with('success', 'Article de stock créé avec succès.');
    }

    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'article' => ['required', 'string', 'max:255'],
            'quantite' => ['required', 'integer', 'min:0'],
            'seuil_alerte' => ['required', 'integer', 'min:1'],
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')->with('success', 'Stock mis à jour avec succès.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Article supprimé avec succès.');
    }
}
