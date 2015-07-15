<?php

namespace App\Findtime;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class TimekitApi
{

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => env('TIMEKIT_URL'),
            'defaults' => [
                'headers' => ['Timekit-App' => 'findtime'],
                'auth'    => [env('TIMEKIT_USER'), env('TIMEKIT_PWD')]
            ]
        ]);
    }

    public function findtime($emails, $future = '2 days', $length = '30 minutes')
    {
        /*$json = json_encode([
            'emails' => $emails,
            'future' => $future,
            'length' => $length,
            'filters' => [
                'after' => ['take_random' => ['number' => 5 ]]
            ]
        ]);*/
        
        $json = '{
            "emails": [
                "timebirdcph@gmail.com"
            ],
            "filters": {
                "after": [
                    { "take_random": {"number": 5} }
                ]
            },
            "future": "1 months",
            "length": "1 hour",
            "sort": "asc"
        }';

        $response = $this->makeRequest('findtime', $json);

        return $response;
    }

    private function makeRequest($url, $body)
    {
        Log::debug('Calling ' . env('TIMEKIT_URL') . $url . ' with body:' . print_r($body, true));
        try {
            $response = $this->client->post($url, ['body' => $body])->json();
            $code = 200;
        } catch (ClientException $exception) {
            $response = $exception->getResponse()->getBody(true)->getContents();
            $code = 500;
            //Log::error($response);
        }

        Log::debug(sprintf('[%s] Timekit returned: %s', $code, print_r($response, true)));
        return ['response' => $response, 'code' => $code];
    }
}
