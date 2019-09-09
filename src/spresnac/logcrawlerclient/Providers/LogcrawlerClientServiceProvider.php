<?php

namespace spresnac\logcrawlerclient\Providers;

use Illuminate\Support\ServiceProvider;
use spresnac\logcrawlerclient\Handler\LogCrawler;

class LogcrawlerClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        
    }

    public function register()
    {
        $this->app->bind('logcrawlerclient', function() {
            return new LogCrawler();
        });
    }
}