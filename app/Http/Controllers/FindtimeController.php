<?php

namespace App\Http\Controllers;

use App\Findtime\TimekitApi;
use App\Findtime\WitLib;
use App\Findtime\WitResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FindtimeController extends Controller
{

    public function query(Request $request)
    {
        $wit = new WitLib();
        $witResponse = $wit->query($request->get('q'));

        $witResponse = new WitResponse($witResponse);

        $timekit = new TimekitApi();
        $tkResponse = $timekit->findtime(
        //$witResponse->getEmails(),
            ['timebirdcph@gmail.com'],
            $witResponse->getFuture(),
            $witResponse->getLength()
        );

        return new Response([
            'timekit' => $tkResponse['response'],
            'wit'     => $witResponse->__toArray()
        ], $tkResponse['code']);
    }

}