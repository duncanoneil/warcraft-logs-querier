<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'wcl' => [
                'oauthUrl' => 'https://www.warcraftlogs.com/oauth/token',
                'clientUrl' => 'https://www.warcraftlogs.com/api/v2/client',
                'clientId' => $_ENV['WCL_CLIENT_ID'],
                'clientSecret' => $_ENV['WCL_CLIENT_SECRET'],
            ],
        ],
    ]);
};
