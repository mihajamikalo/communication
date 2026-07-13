@php
    $title = $title ?? 'Page';
    $subtitle = $subtitle ?? '';
@endphp

@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ $title }}</h2>
    <p class="text-sm text-slate-500">Ce module sera bientôt disponible.</p>
</div>
@endsection
