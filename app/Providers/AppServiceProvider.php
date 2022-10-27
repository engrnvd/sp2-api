<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Naveed\Scaff\ScaffServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(ScaffServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    public function boot()
    {
        //
    }
}
