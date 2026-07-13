@php
    $title = 'Gestion du profil';
    $subtitle = 'Modifier vos informations personnelles';
@endphp

@extends('layouts.app')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
