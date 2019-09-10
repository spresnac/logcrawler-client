<?php

namespace spresnac\logcrawlerclient\Facades;

use Illuminate\Support\Facades\Facade;

class LogcrawlerClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'logcrawlerclient';
    }
}