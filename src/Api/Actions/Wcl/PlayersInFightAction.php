<?php


namespace App\Api\Actions\Wcl;

use Fig\Http\Message\StatusCodeInterface as Code;
use Psr\Http\Message\ResponseInterface as Response;


class PlayersInFightAction extends WclAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $code = $this->resolveArg('code');
        $encounterID = $this->resolveArg('encounterID');
        $startTime = $this->resolveArg('startTime');
        $endTime = $this->resolveArg('endTime');
        $query = '
query ($code: String, $encounterID: Int, $startTime: Float, $endTime: Float) { 
    reportData {
        report(code: $code) {
            code
            title
            zone { name }
            graph(encounterID: $encounterID, startTime: $startTime, endTime: $endTime, dataType: Casts)
        }
    }
}
';
        $options = [
            'body' => json_encode([
                'query' => $query,
                'variables' => [
                    'code' => $code,
                    'encounterID' => $encounterID,
                    'startTime' => $startTime,
                    'endTime' => $endTime
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
            $raidData = $data->data->reportData->report;
            $startTime = $raidData->graph->data->startTime;
            $return = [
                'raid' => [
                    'code' => $raidData->code,
                    'title' => $raidData->title,
                    'zone' => $raidData->zone->name,
                    'starts' => $raidData->graph->data->startTime,
                    'ends' => $raidData->graph->data->endTime,
                ],
                'players' => []
            ];
            foreach ($raidData->graph->data->series as $playerData) {
                $player = [
                    'name' => $playerData->name,
                    'class' => $playerData->type,
                    'firstAction' => $playerData->data[0][0],
                    'firstActionSeconds' => round(($playerData->data[0][0] - $startTime) / 1000, 2),
                ];
                $return['players'][$playerData->id] = $player;
            }
            return $this->respondWithData($return);
        }
        throw new \Exception('Could not get data');
    }
}