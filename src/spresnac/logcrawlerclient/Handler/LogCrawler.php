<?php

namespace spresnac\logcrawlerclient\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractSyslogHandler;

class LogCrawler extends AbstractSyslogHandler
{
    /** @var CurlRequest */
    private $curlRequest;

    /** @var int */
    protected $facility;

    /** @var int */
    protected $level;

    /** @var bool */
    protected $bubble;

    /** @var string */
    protected $ident;

    /** @var int */
    protected $rfc;

    /** @var array */
    private $queue = [];

    public function __construct(
        CurlRequest $curlRequest,
        $facility = LOG_USER,
        $level = Logger::DEBUG,
        bool $bubble = true,
        string $ident = 'laravel',
        int $rfc = -1
    ) {
        parent::__construct($facility, $level, $bubble);
        $this->curlRequest = $curlRequest;
        $this->facility = $facility;
        $this->level = $level;
        $this->bubble = $bubble;
        $this->ident = $ident;
        $this->rfc = $rfc;
        register_shutdown_function([$this, 'sendReports']);
    }

    public function sendReports()
    {
        $this->curlRequest->postToApi($this->queue);
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $data = [
            'params' => [
                'facility' => $this->facility,
                'level'    => $this->level,
                'bubble'   => $this->bubble,
                'ident'    => $this->ident,
                'rfc'      => $this->rfc,
            ],
            'record' => $record,
        ];
        $this->queue[] = $data;
    }
}
