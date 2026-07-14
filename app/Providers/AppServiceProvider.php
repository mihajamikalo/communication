<?php

namespace App\Providers;

use App\Services\AlerteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('fr');
        setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'French');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        if ($root = config('app.url')) {
            URL::forceRootUrl($root);
        }

        View::composer('layouts.partials.header', function ($view) {
            if (! auth()->check()) {
                $view->with('nbAlertes', 0);

                return;
            }

            $view->with('nbAlertes', app(AlerteService::class)->count());
        });
    }
}

