@props(['createRoute' => null, 'createLabel' => 'Ajouter'])

@if($createRoute)
<div class="flex items-center justify-end mb-6">
    <a href="{{ $createRoute }}" class="inline-flex items-center gap-2 bg-escm-primary hover:bg-escm-primary-dark text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        {{ $createLabel }}
    </a>
</div>
@endif
