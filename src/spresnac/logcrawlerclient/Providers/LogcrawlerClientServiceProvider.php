<?php

namespace spresnac\logcrawlerclient\Providers;

use Illuminate\Support\ServiceProvider;
use spresnac\logcrawlerclient\Handler\LogCrawler;

class LogcrawlerClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/logcrawler.php' => base_path('config/logcrawler.php'),
        ], 'logcrawlerclient-config');
    }

    public function register()
    {
        $this->app->bind('logcrawlerclient', function() {
            return new LogCrawler();
        });

        $this->mergeConfigFrom(__DIR__.'/../config/logcrawler.php', 'logcrawler');
    }
}