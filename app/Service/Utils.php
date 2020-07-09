<?php

namespace App\Services;
use Log;

class Utils
{
    public static function responseToObject($resp)
    {
        $response = json_decode($resp->getBody()->getContents());
        Log::info('Response =>');
        Log::info(json_encode($response));
        return $response;
    }

    public static function setHeaderResponse()
    {
        return $header = array (
                'Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'
        );
    }
}
