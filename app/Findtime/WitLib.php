<?php

namespace App\Findtime;

use GuzzleHttp\Client;

class WitLib
{

    private $token = 'ALE5ER2CQL3SBDQH66S5P4EGL564DEOD';
    private $id = '20150509';

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => 'https://api.wit.ai/',
            'defaults' => [
                'headers' => ['Authorization' => 'Bearer ' . $this->token],
                'query'   => ['v' => $this->id],
            ]
        ]);
    }

    public function query($q)
    {
        $response = $this->client->get(sprintf('/message?q=%s', $q));
        return $response->json();
    }

}