<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Compartir la variable $isUserLoggedIn con todas las vistas
        View::composer('*', function ($view) {
            $view->with([
                'isUserLoggedIn' => Auth::check(),
                'custom_email' => 'custom@example.com', // Puedes cambiar esto por el valor deseado
            ]);
        });
    }
}
