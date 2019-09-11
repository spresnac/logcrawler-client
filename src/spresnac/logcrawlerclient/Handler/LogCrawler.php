<?php

namespace spresnac\logcrawlerclient\Handler;

use GuzzleHttp\Client;
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
    }

    protected function write(array $record): void
    {
        $http_client = new Client([
            'base_uri' => $this->host,
        ]);
        $http_client->post('/api/log', [
            'headers' => [
                'X-LC-Key' => $this->key,
            ],
            'form_params' => [
                'params' => [
                    'facility' => $this->facility,
                    'level' => $this->level,
                    'bubble' => $this->bubble,
                    'ident' => $this->ident,
                    'rfc' => $this->rfc,
                ],
                'record' => $record,
            ],
        ]);
    }
}
