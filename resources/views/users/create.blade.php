@php
    $title = 'Nouvel utilisateur';
    $subtitle = 'Création d\'un compte utilisateur';
@endphp

@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
            @csrf
            @include('users._form')
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-escm-primary hover:bg-escm-primary-dark text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors">Créer l'utilisateur</button>
                <a href="{{ route('users.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
