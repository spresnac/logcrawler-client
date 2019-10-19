<?php

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
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
                'test' => 'record',
            ],
        ];

        $this->instance->write($logRecord);
        $this->curlRequestMock->expects($this->once())->method('postToApi');
        $this->instance->sendReports();
    }

    public function testLogEntryPreparation()
    {
        $logRecord = [
            'this' => 'is',
            'a' => [
                'test' => 'record',
            ],
        ];

        $this->instance->write($logRecord);
        $queue = $this->instance->getQueue();
        $queue = $queue[0];
        $this->assertArrayHasKey('params', $queue);
        $this->assertIsArray($queue['params']);
        $this->assertArrayHasKey('facility', $queue['params']);
        $this->assertArrayHasKey('level', $queue['params']);
        $this->assertArrayHasKey('bubble', $queue['params']);
        $this->assertArrayHasKey('ident', $queue['params']);
        $this->assertArrayHasKey('rfc', $queue['params']);
        $this->assertArrayHasKey('record', $queue);
        $this->assertArrayHasKey('this', $queue['record']);
        $this->assertArrayHasKey('a', $queue['record']);
        $this->assertIsArray($queue['record']['a']);
        $this->assertArrayHasKey('test', $queue['record']['a']);
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
