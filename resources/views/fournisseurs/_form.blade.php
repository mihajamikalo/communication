@php $fournisseur = $fournisseur ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nom</label>
    <input type="text" name="nom" value="{{ old('nom', $fournisseur?->nom) }}" required
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('nom')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Téléphone</label>
    <input type="text" name="telephone" value="{{ old('telephone', $fournisseur?->telephone) }}"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('telephone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
    <input type="email" name="email" value="{{ old('email', $fournisseur?->email) }}"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm">
    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">Service</label>
    <input type="text" name="service" value="{{ old('service', $fournisseur?->service) }}"
           class="w-full rounded-lg border-slate-300 shadow-sm focus:border-escm-primary focus:ring-escm-primary text-sm"
           placeholder="Ex. Impression, Production vidéo, Goodies…">
    @error('service')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
