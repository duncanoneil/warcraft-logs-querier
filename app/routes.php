<?php
declare(strict_types=1);

use App\Api\Actions\Wcl;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/api', function (Group $group) {
        $group->get('/raids', Wcl\RaidsAction::class);
        $group->get('/raids/{code}', Wcl\RaidFightsAction::class);
        $group->get('/raids/{code}/{encounterID}/{startTime}/{endTime}', Wcl\PlayersInRaidAction::class);
    });
};
