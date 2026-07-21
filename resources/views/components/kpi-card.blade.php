@props([
    'label',
    'value',
    'subtext' => null,
    'iconColor' => 'blue',
    'progress' => null,
    'progressLabel' => null,
    'progressColor' => 'blue',
    'valueClass' => 'text-slate-900',
])

@php
    $iconColors = [
        'blue' => 'bg-blue-50 text-blue-600',
        'green' => 'bg-green-50 text-green-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'orange' => 'bg-orange-50 text-orange-600',
        'red' => 'bg-red-50 text-red-600',
    ];
    $barColors = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'purple' => 'bg-purple-500',
        'orange' => 'bg-orange-500',
        'red' => 'bg-red-500',
    ];
@endphp

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
    <div class="flex items-start justify-between mb-3">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</p>
        <div class="p-2 rounded-lg {{ $iconColors[$iconColor] ?? $iconColors['blue'] }}">
            {{ $icon }}
        </div>
    </div>
    <p class="text-2xl font-bold mb-1 {{ $valueClass }}">{{ $value }}</p>
    @if($subtext)
        <p class="text-xs text-slate-500 mb-3">{{ $subtext }}</p>
    @endif
    @if($progress !== null)
        <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1">
            <div class="h-1.5 rounded-full {{ $barColors[$progressColor] ?? $barColors['blue'] }}" style="width: {{ min(100, max(0, $progress)) }}%"></div>
        </div>
        <p class="text-[11px] {{ $progressColor === 'red' ? 'text-red-600 font-medium' : 'text-slate-500' }}">{{ $progressLabel }}</p>
    @endif
</div>
