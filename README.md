# Laravel Logcrawler Client
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHP from Packagist](https://img.shields.io/packagist/php-v/spresnac/logcrawler-client.svg)
![Downloads](https://img.shields.io/packagist/dt/spresnac/logcrawler-client.svg)
![StyleCI](https://github.styleci.io/repos/207257104/shield)

## Requirement
You will need a free account on [Logcrawler Server](https://logcrawler.de "Logcrawler Server") to be able to use your logcrawler-key here 😉

## Installation
First things first, require the package
```
composer require spresnac/logcrawler-client
```

Second, publish the default config file
```
php artisan vendor:publish --tag=logcrawlerclient-config
```

## Configuration

Next, edit your `.env` so you can put your Logcrawler project key
```
LOG_CRAWLER_KEY="place_your_key_here"
```

After this, edit your `/config/logging` and append this at `channels`:

Laravel up to 5.7 (including):
```php
'channels' => [
    //...
    'logcrawler' => [
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => LogCrawler::class,
        'handler_with' => [
            'host' => config('logcrawler.url'),
            'key' => config('logcrawler.key'),
        ],
    ],
    //...
],
```

Laravel 5.8+ and 6.x:
```php
'channels' => [
    //...
    'logcrawler' => [
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => LogCrawler::class,
        'with' => [
            'host' => config('logcrawler.url'),
            'key' => config('logcrawler.key'),
        ],
    ],
    //...
],
```

Last thing, add the `logcrawler` channel to your selected channel, example:
```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'logcrawler'],
    'ignore_exceptions' => false,
],
```

## Sending PHP and laravel version to your server (v2.1.0+ client and server)
To have an oversight of your installed version, logcrawler-client can send this information to your server. Both must have at least v2.1.0 or higher!

By default, sending this information is set to `false`, you can set each option by itself in your `.env`:

`LOGCRAWLER_INFO_PHP` => (bool, default false) Send PHP version to your server?


`LOGCRAWLER_INFO_LARAVEL` => (bool, default false) Send laravel version to your server?

Now, you can run the command or shedule it (once a week or so) as you like in your app.

`php artisan logcrawler:client:sendversions`

## Finally
Now, your logging to Logcrawler is enabled and you can watch your logs.
Have fun 😎

## Options
### force_threshold
By default, logcrawler sends its logs when your php process exits and for default, that is preventing logcrawler from slowing down your app process.
In some circumstances, you may wish to like "force sending" logs, i.e. when running in a queue. In this case, you can enable a threshold in your `.env` with
```
LOG_CRAWLER_THRESHOLD=<INT>
``` 
When not present or set to 0 (default), logcrawler only sends reports when your php exits.  
All other integer values are your threshold for sending the logs!  
Notice: A number too small can slow down your app - try to start with 4