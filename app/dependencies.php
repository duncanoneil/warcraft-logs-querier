<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use GuzzleHttp\Client as GuzzleClient;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        GuzzleClient::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $wclSettings = $settings['wcl'];
            return new GuzzleClient([
                'base_uri' => $wclSettings['clientUrl'],
                'timeout'  => 2.0,
            ]);
        },
        'view' => function (ContainerInterface $c) {
            return Twig::create(__DIR__ . '/../templates',
                ['cache' => false]);
        },
    ]);
};
