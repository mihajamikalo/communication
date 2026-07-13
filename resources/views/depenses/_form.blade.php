@php $depense = $depense ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Date</label>
        <input type="date" name="date_depense" value="{{ old('date_depense', $depense?->date_depense?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
        @error('date_depense')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Montant (Ar)</label>
        <input type="number" name="montant" value="{{ old('montant', $depense?->montant) }}" required min="0" step="1"
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
        @error('montant')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Fournisseur</label>
    <input type="text" name="fournisseur" list="fournisseurs-list" value="{{ old('fournisseur', $depense?->fournisseur) }}" required
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    <datalist id="fournisseurs-list">
        @foreach($fournisseurs as $nom)
            <option value="{{ $nom }}">
        @endforeach
    </datalist>
    @error('fournisseur')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Objet</label>
    <input type="text" name="objet" value="{{ old('objet', $depense?->objet) }}" required
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('objet')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Campagne</label>
    <input type="text" name="campagne" list="campagnes-list" value="{{ old('campagne', $depense?->campagne) }}"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    <datalist id="campagnes-list">
        @foreach($campagnes as $nom)
            <option value="{{ $nom }}">
        @endforeach
    </datalist>
    @error('campagne')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Catégorie</label>
        <select name="categorie" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
            @foreach($categories as $value => $label)
                <option value="{{ $value }}" @selected(old('categorie', $depense?->categorie) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('categorie')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Statut</label>
        @php
            $canApprove = $canApprove ?? (auth()->user()?->canApproveDepense() ?? false);
            $currentStatut = old('statut', $depense?->statut ?? 'en_attente');
            $isLockedApproved = ! $canApprove && $depense && $depense->statut === 'valide';
        @endphp

        @if($isLockedApproved)
            <input type="hidden" name="statut" value="valide">
            <div class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
                Approuvé <span class="text-blue-600 text-xs">(modifiable uniquement par un Super Admin)</span>
            </div>
        @else
            <select name="statut" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
                @foreach($statuts as $value => $label)
                    <option value="{{ $value }}" @selected($currentStatut === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @unless($canApprove)
                <p class="mt-1.5 text-xs text-slate-500">Seul un Super Admin peut passer le statut à « Approuvé ».</p>
            @endunless
        @endif
        @error('statut')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
