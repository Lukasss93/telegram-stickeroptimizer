<?php

namespace App\Providers;

use App\Support\ImageUtils;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //load helpers folder
        foreach (glob(app_path().'/Helpers/*.php') as $file) {
            require_once($file);
        }

        $this->app->bind('ImageUtils', function () {
            return new ImageUtils();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
