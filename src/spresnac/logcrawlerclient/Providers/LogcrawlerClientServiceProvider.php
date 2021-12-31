<?php

namespace spresnac\logcrawlerclient\Providers;

use Illuminate\Support\ServiceProvider;

class LogcrawlerClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/logcrawler.php' => base_path('config/logcrawler.php'),
        ], 'logcrawlerclient-config');
        $this->publishes([
            __DIR__.'/../Console/Commands/Selfdiagnostic.php' => base_path('app/Console/Commands/Selfdiagnostic.php'),
        ], 'logcrawlerclient-commands');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/logcrawler.php', 'logcrawler');
    }
}
