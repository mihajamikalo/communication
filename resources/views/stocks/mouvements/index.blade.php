@php
    $title = 'Entrées / Sorties';
    $subtitle = 'Mouvements de stock marketing (flyers, brochures, goodies…)';
@endphp

@extends('layouts.app')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <form method="GET" action="{{ route('stocks.mouvements.index') }}" class="flex flex-wrap items-center gap-2">
        <select name="type" class="rounded-lg border-slate-300 text-sm focus:border-escm-primary focus:ring-escm-primary">
            <option value="">Tous les types</option>
            <option value="entree" @selected(request('type') === 'entree')>Entrées</option>
            <option value="sortie" @selected(request('type') === 'sortie')>Sorties</option>
        </select>
        <select name="stock_id" class="rounded-lg border-slate-300 text-sm focus:border-escm-primary focus:ring-escm-primary">
            <option value="">Tous les articles</option>
            @foreach($stocks as $stock)
                <option value="{{ $stock->id }}" @selected(request('stock_id') == $stock->id)>{{ $stock->article }} ({{ $stock->quantite }})</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 text-sm font-medium rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">Filtrer</button>
    </form>

    <div class="flex items-center gap-2">
        <a href="{{ route('stocks.mouvements.create', ['type' => 'entree']) }}" class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Entrée
        </a>
        <a href="{{ route('stocks.mouvements.create', ['type' => 'sortie']) }}" class="inline-flex items-center gap-1.5 bg-escm-primary hover:bg-escm-primary-dark text-white text-sm font-medium px-4 py-2.5 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
            Sortie
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100 bg-slate-50/50">
                    <th class="px-5 py-3">Date</th>
                    <th class="px-3 py-3">Type</th>
                    <th class="px-3 py-3">Article</th>
                    <th class="px-3 py-3 text-right">Quantité</th>
                    <th class="px-3 py-3 hidden md:table-cell">Motif</th>
                    <th class="px-3 py-3 hidden lg:table-cell">Référence</th>
                    <th class="px-3 py-3 text-center hidden sm:table-cell">Stock actuel</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($mouvements as $mouvement)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $mouvement->date_mouvement->format('d/m/Y') }}</td>
                    <td class="px-3 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $mouvement->type === 'entree' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $mouvement->type_label }}
                        </span>
                    </td>
                    <td class="px-3 py-3 font-medium text-slate-900">{{ $mouvement->stock?->article ?? '—' }}</td>
                    <td class="px-3 py-3 text-right font-semibold {{ $mouvement->type === 'entree' ? 'text-green-700' : 'text-orange-700' }}">
                        {{ $mouvement->type === 'entree' ? '+' : '−' }}{{ $mouvement->quantite }}
                    </td>
                    <td class="px-3 py-3 text-slate-600 hidden md:table-cell">{{ $mouvement->motif ?? '—' }}</td>
                    <td class="px-3 py-3 text-slate-500 hidden lg:table-cell">{{ $mouvement->reference ?? '—' }}</td>
                    <td class="px-3 py-3 text-center text-slate-700 hidden sm:table-cell">{{ $mouvement->stock?->quantite ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <x-row-actions
                            :edit-route="route('stocks.mouvements.edit', $mouvement)"
                            :delete-route="route('stocks.mouvements.destroy', $mouvement)"
                            delete-confirm="Supprimer ce mouvement ? Le stock sera rétabli automatiquement."
                        />
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-slate-500">Aucun mouvement enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mouvements->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $mouvements->links() }}</div>
    @endif
</div>
@endsection
