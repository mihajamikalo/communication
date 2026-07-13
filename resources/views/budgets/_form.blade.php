@php $budget = $budget ?? null; $isEdit = $edit ?? false; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Montant (Ar)</label>
    <input type="number" name="montant" value="{{ old('montant', $budget?->montant) }}" required min="0" step="1"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('montant')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Année</label>
        <input type="number" name="annee" value="{{ old('annee', $budget?->annee ?? now()->year) }}" required min="2020" max="2100"
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
        @error('annee')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Mois</label>
        <select name="mois" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" @selected(old('mois', $budget?->mois ?? now()->month) == $m)>
                    {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->isoFormat('MMMM') }}
                </option>
            @endforeach
        </select>
        @error('mois')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
