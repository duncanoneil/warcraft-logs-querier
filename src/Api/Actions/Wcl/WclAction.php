<?php


namespace App\Api\Actions\Wcl;

use App\Api\Actions\Action;
use Fig\Http\Message\StatusCodeInterface as Code;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;


abstract class WclAction extends Action
{
    /**
     * @var GuzzleClient
     */
    protected $client;
    protected $token;

    /**
     * @param LoggerInterface $logger
     * @param GuzzleClient  $client
     */
    public function __construct(LoggerInterface $logger, GuzzleClient $client)
    {
        parent::__construct($logger);
        $this->client = $client;
        $this->token = $this->getAuthToken();
    }

    /**
     * @return object
     * @throws GuzzleException
     */
    protected function getAuthToken(): object
    {
        $authUrl = 'https://www.warcraftlogs.com/oauth/token';
        $options = [
            'auth' => [$_ENV['WCL_CLIENT_ID'], $_ENV['WCL_CLIENT_SECRET']],
            'form_params' => ['grant_type' => 'client_credentials']
        ];

        $response = $this->client->post($authUrl, $options);
        if ($response->getStatusCode() === Code::STATUS_OK) {
            return json_decode($response->getBody()->getContents());
        }
        throw new \Exception('Unable to authorize');
    }
}