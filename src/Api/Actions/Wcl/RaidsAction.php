<?php


namespace App\Api\Actions\Wcl;

use Fig\Http\Message\StatusCodeInterface as Code;
use Psr\Http\Message\ResponseInterface as Response;

class RaidsAction extends WclAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $query = '
query {
    reportData {
        reports(guildID: 485079, limit: 10) {
            data {
                code
                title
                zone {
                    id
                    name
                }
                owner {
                    name
                }
            }
        }
    }
}
';
        $options = [
            'body' => json_encode([
                'query' => $query,
            ]),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token->access_token,
                'Content-Type' => 'application/json'
            ]
        ];

        $response = $this->client->get('', $options);
        if ($response->getStatusCode() === Code::STATUS_OK) {
            $data = json_decode($response->getBody()->getContents());
            $return = $data->data->reportData->reports->data;
            return $this->respondWithData($return);
        }
        throw new \Exception('Could not get data');
    }
}