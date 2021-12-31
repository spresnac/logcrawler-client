<?php

return [

    'key' => env('LOG_CRAWLER_KEY'),

    'url' => env('LOG_CRAWLER_URL', 'https://logcrawler.de'),

    'force_threshold' => env('LOG_CRAWLER_THRESHOLD', 0),

    'bearer_token' => env('LOG_CRAWLER_BEARER_TOKEN'),

    'header_key' => env('LOGCRAWLER_HEADER_KEY'),

];
