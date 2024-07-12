<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        //Schema::defaultStringLength(191);//esta linea es nueva
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar','en','fr','es']); // also accepts a closure
        });
    }
}
