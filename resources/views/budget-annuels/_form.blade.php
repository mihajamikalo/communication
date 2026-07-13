@php $budgetAnnuel = $budgetAnnuel ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Année</label>
    <input type="number" name="annee" value="{{ old('annee', $budgetAnnuel?->annee ?? now()->year) }}" required min="2020" max="2100"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('annee')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Montant annuel (Ar)</label>
    <input type="number" name="montant" value="{{ old('montant', $budgetAnnuel?->montant) }}" required min="0" step="1"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('montant')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    <p class="mt-1.5 text-xs text-slate-500">Ce montant sera déduit automatiquement à chaque création / modification de budget mensuel.</p>
</div>
