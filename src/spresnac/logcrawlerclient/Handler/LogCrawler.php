<?php

namespace spresnac\logcrawlerclient\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractSyslogHandler;
use spresnac\logcrawlerclient\Request\LogCrawlerCurlRequest;

class LogCrawler extends AbstractSyslogHandler
{
    /** @var LogCrawlerCurlRequest */
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
        LogCrawlerCurlRequest $curlRequest,
        bool $bubble = true,
        $facility = LOG_USER,
        $level = Logger::DEBUG,
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

    /**
     * @return array
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param array $record
     */
    public function write(array $record): void
    {
        $this->queue[] = $this->prepareLogEntry($record);
    }

    /**
     * send queue to api
     */
    public function sendReports()
    {
        if (count($this->queue) <= 0) {
            return;
        }

        $this->curlRequest->postToApi($this->queue);
    }

    private function prepareLogEntry($record)
    {
        return [
            'params' => [
                'facility' => $this->facility,
                'level'    => $this->level,
                'bubble'   => $this->bubble,
                'ident'    => $this->ident,
                'rfc'      => $this->rfc,
            ],
            'record' => $record,
        ];
    }
}
