<?php

use Monolog\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spresnac\logcrawlerclient\Handler\LogCrawler;
use spresnac\logcrawlerclient\Request\LogCrawlerCurlRequest;

class LogCrawlerTest extends TestCase
{
    /** @var LogCrawlerCurlRequest|MockObject */
    private $curlRequestMock;

    /** @var LogCrawler */
    private $instance;

    protected function setUp(): void
    {
        $this->curlRequestMock = $this->getMockBuilder(LogCrawlerCurlRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = new LogCrawler(
            $this->curlRequestMock,
            true,
            LOG_USER,
            Logger::DEBUG,
            'test-identifier',
            -1
        );
    }

    public function testQueueWillBeSentToApi()
    {
        $logRecord = [
            'this' => 'is',
            'a' => [
                'test' => 'record'
            ]
        ];

        $this->instance->write($logRecord);
        $this->curlRequestMock->expects($this->once())->method('postToApi');
        $this->instance->sendReports();
    }

    public function testEmptyQueueWillNotBeSent()
    {
        $this->curlRequestMock->expects($this->never())->method('postToApi');
        $this->instance->sendReports();
    }

    protected function tearDown(): void
    {
        $this->instance = null;
        parent::tearDown();
    }
}
