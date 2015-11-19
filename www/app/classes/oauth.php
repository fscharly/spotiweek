<?php

/**
* This class is used for every O-Auth action.
*/

namespace App;

class Oauth
{
    /**
    * Build URL where the user must be redirect to retrieve a code. User will
    * have to login then will be redirect to $redirect_uri where the code will
    * be stored in session and cookie.
    * The code obtained will give access to a token that you have to use to
    * every spotify's api call.
    */
    public static function getAuthorizeUrl($client_id, $redirect_uri, $scopes)
    {
        $url = 'https://accounts.spotify.com/authorize';
        $param = array(
            'response_type' => 'code',
            'client_id' => $client_id,
            'scope' => urlencode($scopes),
            'redirect_uri' => urlencode($redirect_uri),
            'show_dialog' => 'true',
        );
        $data = array();
        foreach ($param as $key => $value) {
            $data[] = $key.'='.$value;
        }
        return $url.'?'.implode('&', $data);
    }

    public static function getToken()
    {

    }

    public static function refreshToken()
    {

    }

    public static function isTokenExpired()
    {

    }
}
