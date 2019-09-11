# Laravel Logcrawler Client
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHP from Packagist](https://img.shields.io/packagist/php-v/spresnac/logcrawler-client.svg)
![Downloads](https://img.shields.io/packagist/dt/spresnac/logcrawler-client.svg)
![StyleCI](https://github.styleci.io/repos/207257104/shield)

## Requirement
You will need an account and a project on Logcrawler-Server to be able to use your logcrawler-key here 😉

## Installation
First of all, require the package
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

Last thing, add the `logcrawler` channel to your selected channel, example:
```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['daily', 'logcrawler'],
    'ignore_exceptions' => false,
],
```

## Finally
Now, your logging to Logcrawler is enabled and you can watch your logs.

Have fun 😎
