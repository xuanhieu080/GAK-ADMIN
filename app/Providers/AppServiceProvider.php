<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Supports\CustomFilesystem;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            \Spatie\MediaLibrary\MediaCollections\Filesystem::class,
            CustomFilesystem::class
        );
    }
}
