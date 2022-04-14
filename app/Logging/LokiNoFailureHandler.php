<?php

declare(strict_types=1);

namespace App\Logging;

use Itspire\MonologLoki\Formatter\LokiFormatter;
use Itspire\MonologLoki\Handler\LokiHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Monolog\Logger;

class LokiNoFailureHandler
{
    public function __invoke(array $config)
    {
        return new Logger('loki-no-failure', [
            new WhatFailureGroupHandler([
                (new LokiHandler($config['handler_with']['apiConfig'], $config['level']))
                    ->setFormatter(new LokiFormatter(...array_values($config['formatter_with']))),
            ]),
        ]);
    }
}
