@php $stock = $stock ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Article</label>
    <input type="text" name="article" value="{{ old('article', $stock?->article) }}" required
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('article')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Quantité en stock</label>
        <input type="number" name="quantite" value="{{ old('quantite', $stock?->quantite ?? 0) }}" required min="0"
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
        @error('quantite')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Seuil d'alerte</label>
        <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', $stock?->seuil_alerte ?? 50) }}" required min="1"
               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
        @error('seuil_alerte')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
