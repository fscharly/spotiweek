<?php

/**
* Used to wrap RPC.
*/

namespace App;

class Curl
{

    /**
    * Call an url with parameters in POST
    * @param $url Url to call
    * @param $data Data to send. It can be a string that will be added without
    * any processing, or an associative array that will be convert into
    * key1=val1&key2=val2 format
    * @param $header An associative array that will be send in the request header.
    * Value will get converted in $key: $val format.
    * @return Http request response with no parsning
    */
    public static function post($url, $data, $header = false)
    {
        $parsedHeader = array();
        if ($header) {
            foreach ($header as $key => $value) {
                $parsedHeader[] = $key.': '.$value;
            }
        }

        if (is_array($data)) {
            $data = http_build_query($data);
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $parsedHeader);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
    * Call an url with parameters in GET
    * @param $url Url to call
    * @param $data Data to send. It must be an associative array that will be
    * convert into key1=val1&key2=val2 format added to the end of the URL. If
    * any data must be sent, set it to false.
    * @param $header An associative array that will be send in the request header.
    * Value will get converted in $key: $val format.
    * @return Http request response with no parsning
    */
    public static function get($url, $data, $header = false)
    {
        $parsedHeader = array();
        if ($header) {
            foreach ($header as $key => $value) {
                $parsedHeader[] = $key.': '.$value;
            }
        }

        if ($data) {
            $parsedData = http_build_query($data);
        } else {
            $parsedData = array();
        }

        $curl = curl_init($url.($data && count($data) > 0 ? '?'. $parsedData : ''));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $parsedHeader);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
