@props([
    'user' => null,
    'name' => null,
    'initials' => null,
    'avatarUrl' => null,
    'size' => 'md',
    'ring' => false,
])

@php
    $displayName = $name ?? $user?->name ?? '';
    $displayInitials = $initials ?? ($user ? $user->initials() : '?');
    $url = $avatarUrl ?? $user?->avatar_url;

    $sizes = [
        'xs' => 'h-6 w-6 text-[10px]',
        'sm' => 'h-7 w-7 text-[10px]',
        'md' => 'h-8 w-8 text-xs',
        'lg' => 'h-9 w-9 text-sm',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $ringClass = $ring ? 'ring-2 ring-white' : '';
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex {$sizeClass} {$ringClass} items-center justify-center rounded-full bg-escm-primary font-bold text-white overflow-hidden shrink-0",
        'title' => $displayName,
    ]) }}
>
    @if($url)
        <img src="{{ $url }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
    @else
        {{ $displayInitials }}
    @endif
</span>
