<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Selfdiagnostic extends Command
{
    protected $signature = 'logcrawler:client:selftest';
    protected $description = 'Checks, if all steps are taken and client is ready to operate';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Selfdiagnostic started...');
        $this->comment('-------------------------');
        $this->outputResult('Bearer Key exist', $this->checkEnvBearerKey());
        $this->outputResult('Project Key exist', $this->checkEnvProjectKey());
        $this->outputResult('Project Host URL exist', $this->checkEnvServerUrl());
        $this->outputResult('Header Key exist', $this->checkEnvHeaderKey());
        $this->info('... Selfdiagnostic end');
        $this->comment('-------------------------');
    }
    
    protected function outputResult(string $text, bool $result): void
    {
        $this->line(
            string: $text . ' ' . ($result ? '✔' : '❌'),
            style: $result ? 'info' : 'error',
        );
    }
    
    protected function checkEnvBearerKey(): bool
    {
        return env('LOG_CRAWLER_BEARER_TOKEN') !== null;
    }
    
    protected function checkEnvProjectKey(): bool
    {
        return env('LOG_CRAWLER_KEY') !== null;
    }
    
    protected function checkEnvServerUrl(): bool
    {
        return env('LOG_CRAWLER_URL') !== null;
    }
    
    protected function checkEnvHeaderKey(): bool
    {
        return env('LOGCRAWLER_HEADER_KEY') !== null;
    }
}