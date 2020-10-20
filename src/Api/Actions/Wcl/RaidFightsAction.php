<?php


namespace App\Api\Actions\Wcl;

use Fig\Http\Message\StatusCodeInterface as Code;
use Psr\Http\Message\ResponseInterface as Response;

class RaidFightsAction extends WclAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $code = $this->resolveArg('code');
        $query = '
query ($code: String) { 
    reportData {
        report(code: $code) {
            code
            title
            startTime
            endTime
            fights(killType: Kills) {
                id
                encounterID
                startTime
                endTime
            }
            rankedCharacters {
                id 
                classID
                name 
            }
        }
    }
}
';
        $options = [
            'body' => json_encode([
                'query' => $query,
                'variables' => [
                    'code' => $code
                ]
            ]),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token->access_token,
                'Content-Type' => 'application/json'
            ]
        ];

        $response = $this->client->get('', $options);
        if ($response->getStatusCode() === Code::STATUS_OK) {
            $data = json_decode($response->getBody()->getContents());
            $return = $data->data->reportData->report;
            return $this->respondWithData($return);
        }
        throw new \Exception('Could not get data');
    }
}