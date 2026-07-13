@php
    $title = 'Stocks';
    $subtitle = 'Stock marketing';
@endphp

@extends('layouts.app')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <p class="text-sm text-slate-500">Les quantités se mettent à jour via <a href="{{ route('stocks.mouvements.index') }}" class="text-escm-primary font-medium hover:underline">Entrées / Sorties</a>.</p>
    <div class="flex items-center gap-2">
        <a href="{{ route('stocks.mouvements.create', ['type' => 'sortie']) }}" class="inline-flex items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg">
            Entrée / Sortie
        </a>
        <a href="{{ route('stocks.create') }}" class="inline-flex items-center gap-1.5 bg-escm-primary hover:bg-escm-primary-dark text-white text-sm font-medium px-4 py-2.5 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvel article
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100 bg-slate-50/50">
                    <th class="px-5 py-3">Article</th>
                    <th class="px-3 py-3 text-center">Quantité</th>
                    <th class="px-3 py-3 text-center">Seuil alerte</th>
                    <th class="px-3 py-3">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stocks as $stock)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-5 py-3 font-medium text-slate-900">{{ $stock->article }}</td>
                    <td class="px-3 py-3 text-center text-slate-700">{{ $stock->quantite }}</td>
                    <td class="px-3 py-3 text-center text-slate-500">{{ $stock->seuil_alerte }}</td>
                    <td class="px-3 py-3"><x-status-badge :statut="$stock->statut" /></td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('stocks.mouvements.create', ['stock_id' => $stock->id, 'type' => 'sortie']) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-escm-primary hover:bg-blue-50" title="Sortie">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </a>
                            <a href="{{ route('stocks.mouvements.create', ['stock_id' => $stock->id, 'type' => 'entree']) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-green-600 hover:bg-green-50" title="Entrée">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </a>
                            <x-row-actions
                                :edit-route="route('stocks.edit', $stock)"
                                :delete-route="route('stocks.destroy', $stock)"
                                delete-confirm="Supprimer cet article ?"
                            />
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Aucun article en stock.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stocks->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $stocks->links() }}</div>
    @endif
</div>
@endsection
