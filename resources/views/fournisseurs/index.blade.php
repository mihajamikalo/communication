@php
    $title = 'Fournisseurs';
    $subtitle = 'Répertoire des fournisseurs';
@endphp

@extends('layouts.app')

@section('content')
<x-page-actions :create-route="route('fournisseurs.create')" create-label="Nouveau fournisseur" />

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100 bg-slate-50/50">
                    <th class="px-5 py-3">Nom</th>
                    <th class="px-3 py-3">Téléphone</th>
                    <th class="px-3 py-3">Email</th>
                    <th class="px-3 py-3 hidden lg:table-cell">Service</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($fournisseurs as $fournisseur)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-5 py-3 font-medium text-slate-900">{{ $fournisseur->nom }}</td>
                    <td class="px-3 py-3 text-slate-600">{{ $fournisseur->telephone }}</td>
                    <td class="px-3 py-3 text-slate-600">{{ $fournisseur->email }}</td>
                    <td class="px-3 py-3 text-slate-600 hidden lg:table-cell">{{ $fournisseur->service ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <x-row-actions
                            :edit-route="route('fournisseurs.edit', $fournisseur)"
                            :delete-route="route('fournisseurs.destroy', $fournisseur)"
                            delete-confirm="Supprimer ce fournisseur ?"
                        />
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-500">Aucun fournisseur enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fournisseurs->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">{{ $fournisseurs->links() }}</div>
    @endif
</div>
@endsection
