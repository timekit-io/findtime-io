<?php

namespace App\Findtime;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TimekitApi
{

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => env('TIMEKIT_URL'),
            'defaults' => [
                'headers' => ['Timekit-App' => 'findtime'],
                'auth'    => ['timebirdcph@gmail.com', 'password']
            ]
        ]);
    }

    public function findtime($emails, $future = '2 days', $length = '30 minutes')
    {
        $json = json_encode([
            'emails' => $emails,
            'future' => $future,
            'length' => $length
        ]);

        $response = $this->makeRequest('findtime', $json);

        return $response;
    }

    private function makeRequest($url, $body)
    {
        try {
            $response = $this->client->post($url, ['body' => $body])->json();
        } catch (ClientException $exception) {
            $response = $exception->getResponse()->json();
            //Log::error($response);
        }

        return $response;
    }
}