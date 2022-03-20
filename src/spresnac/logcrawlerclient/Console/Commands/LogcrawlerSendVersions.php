<?php

namespace spresnac\logcrawlerclient\Console\Commands;

use Illuminate\Console\Command;

class LogcrawlerSendVersions extends Command
{
    protected $signature = 'logcrawler:client:sendversions';
    protected $description = 'Will send the versions of php and/or laravel to the server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (config('logcrawler.key') === null) {
            return;
        }
        if (config('logcrawler.info.send_php') === false && config('logcrawler.info.send_version') === false) {
            return;
        }
        $data = [];
        if (config('logcrawler.info.send_php')) {
            $data['php'] = phpversion();
        }
        if (config('logcrawler.info.send_laravel')) {
            $data['laravel'] = \Illuminate\Foundation\Application::VERSION;
        }

        return $this->send($data);
    }

    /**
     * @param $data
     * @return bool|string
     */
    private function send($data): bool|string
    {
        $data_encoded = json_encode($data);
        $curl_handle = curl_init(config('logcrawler.url').'/api/v2/info');
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
            'Accept:application/json',
            'Content-Type:application/json',
            'x-lc-key:'.config('logcrawler.key'),
            'Authorization:Bearer '.config('logcrawler.bearer_token'),
            'Content-Length:'.mb_strlen($data_encoded),
            'Cache-Control:no-cache',
        ]);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Laravel/Logcrawler');
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl_handle, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($curl_handle, CURLOPT_HEADER, true);
        curl_setopt($curl_handle, CURLOPT_NOBODY, true);
        curl_setopt($curl_handle, CURLOPT_NOPROGRESS, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYSTATUS, false);
        curl_setopt($curl_handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_NONE);
        curl_setopt($curl_handle, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($curl_handle, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl_handle, CURLOPT_POST, true);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data_encoded);
        curl_setopt($curl_handle, CURLOPT_DEFAULT_PROTOCOL, 'https');

        return curl_exec($curl_handle);
    }
}
