<?php

namespace spresnac\logcrawlerclient\Handler;

use Monolog\Handler\AbstractSyslogHandler;
use Monolog\Logger;

class LogCrawler extends AbstractSyslogHandler
{
    protected $facility;
    protected $level;
    protected $bubble;
    private array $queue = [];
    protected string $ident;

    public function __construct($facility = LOG_USER, $level = Logger::DEBUG, bool $bubble = true, string $ident = 'laravel')
    {
        $this->facility = $facility;
        $this->level = $level;
        $this->bubble = $bubble;
        $this->ident = $ident;
        register_shutdown_function([$this, 'sendReports']);
        parent::__construct($facility, $level, $bubble);
    }

    public function sendReports()
    {
        $this->postToApi($this->queue);
        $this->queue = [];
    }

    private function postToApi(array $data)
    {
        if (count($data) <= 0) {
            return;
        }
        if (config('logcrawler.key') === null) {
            return;
        }
        $data_encoded = json_encode($data);
        $curl_handle = curl_init(config('logcrawler.url').'/api/v2/log');
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

        $result = curl_exec($curl_handle);
        $this->handleResultInfo($curl_handle);

        return $result;
    }

    protected function handleResultInfo($curl_handle)
    {
        $info = curl_getinfo($curl_handle);
        if ($info['http_code'] !== 201) {
            $log = new Logger('lc-client');
            $log->debug('unable to send result', $info);
        }
    }

    protected function write(array $record): void
    {
        $data = [
            'params' => [
                'facility' => $this->facility,
                'level' => $this->level,
                'bubble' => $this->bubble,
                'ident' => $this->ident,
            ],
            'record' => $record,
        ];
        $this->queue[] = $data;
        if (count($this->queue) >= config('logcrawler.force_threshold', 0)) {
            $this->sendReports();
        }
    }
}
