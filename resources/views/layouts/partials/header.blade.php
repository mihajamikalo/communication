<header class="sticky top-0 z-30 bg-slate-100/80 backdrop-blur-sm border-b border-slate-200">
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $title ?? 'Dashboard' }}</h1>
                @isset($subtitle)
                    <p class="text-sm text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endisset
            </div>
        </div>

        <div class="flex items-center gap-3 sm:gap-5">
            {{-- Date picker --}}
            <div class="hidden sm:flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 shadow-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="font-medium capitalize">{{ $moisLabel ?? now()->locale('fr')->isoFormat('MMMM YYYY') }}</span>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>

            {{-- Notifications --}}
            <a href="{{ route('dashboard') }}#alertes" class="relative p-2 rounded-lg text-slate-500 hover:bg-white hover:text-slate-700 transition-colors" title="Alertes">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @if(($nbAlertes ?? 0) > 0)
                    <span class="absolute top-1 right-1 flex h-4 min-w-[1rem] px-0.5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $nbAlertes > 9 ? '9+' : $nbAlertes }}</span>
                @endif
            </a>

            {{-- User profile --}}
            @include('layouts.partials.header-user')
        </div>
    </div>
</header>
