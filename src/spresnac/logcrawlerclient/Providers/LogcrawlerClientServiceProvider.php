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
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/logcrawler.php', 'logcrawler');
    }
}