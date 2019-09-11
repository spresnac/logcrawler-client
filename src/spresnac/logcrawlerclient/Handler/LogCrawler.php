<?php

namespace spresnac\logcrawlerclient\Handler;

use Illuminate\Log\Logger;
use Monolog\Handler\AbstractSyslogHandler;

class LogCrawler extends AbstractSyslogHandler
{
    protected $host;
    protected $key;
    protected $facility;
    protected $level;
    protected $bubble;
    protected $ident;
    protected $rfc;
    private $queue = [];

    public function __construct(string $host, $key = null, $facility = LOG_USER, $level = Logger::DEBUG, bool $bubble = true, string $ident = 'laravel', int $rfc = -1)
    {
        parent::__construct($facility, $level, $bubble);
        $this->host = $host;
        $this->key = $key;
        $this->facility = $facility;
        $this->level = $level;
        $this->bubble = $bubble;
        $this->ident = $ident;
        $this->rfc = $rfc;
        register_shutdown_function([$this, 'sendReports']);
    }

    public function sendReports()
    {
        $this->postToApi($this->queue);
    }

    private function postToApi(array $data)
    {
        if (count($data) <= 0) {
            return;
        }
        if ($this->key === null) {
            return;
        }
        $data_encoded = json_encode($data);
        $curl_handle = curl_init($this->host.'/api/log');
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'x-lc-key: '.$this->key,
        ]);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Laravel/Logcrawler');
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl_handle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl_handle, CURLOPT_ENCODING, '');
        curl_setopt($curl_handle, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl_handle, CURLOPT_POST, true);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data_encoded);
        curl_exec($curl_handle);
    }

    protected function write(array $record): void
    {
        $data = [
            'params' => [
                'facility' => $this->facility,
                'level' => $this->level,
                'bubble' => $this->bubble,
                'ident' => $this->ident,
                'rfc' => $this->rfc,
            ],
            'record' => $record,
        ];
        $this->queue[] = $data;
    }
}
