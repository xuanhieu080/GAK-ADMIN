<?php

namespace App\Providers;

use App\Models\Config;
use Illuminate\Support\Facades\Config as AppConfig;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $configs = Config::whereNotNull('config')
            ->whereNotNull('value')->get();

        foreach ($configs as $config) {
            AppConfig::set($config->config,$config->value);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
