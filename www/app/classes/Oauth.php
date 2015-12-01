<?php

/**
* Everything about Sopitify OAuth 2.0 implementation can be found at
* https://developer.spotify.com/web-api/authorization-guide/
*/

namespace App;

class Oauth
{
    static private $instance = false;

    private $client_id = false;
    private $client_secret = false;
    private $redirect_url = false;

    private $access_token = false;
    private $refresh_token = false;
    private $expire_date = false;

    const OAUTH_AUTH_ERROR = 1;

    /**
    * Buid the object and check if any token can be found in $_SESSION.
    * @param $client_id Spotify api credential
    * @param $client_secret Spotify api credential
    * @return Object instance
    */
    private function __construct($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        $this->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].'/authentification/callback';

        if (\App\Session::get('access_token')) {
            $this->access_token = \App\Session::get('access_token');
        }

        if (\App\Session::get('refresh_token')) {
            $this->refresh_token = \App\Session::get('refresh_token');
        }

        if (\App\Session::get('expire_date')) {
            $this->expire_date = new \DateTime(\App\Session::get('expire_date'));
        }

    }

    /**
    * Create an instance of the object and store it if it has never been done,
    * and return the stored instance (singleton).
    * @param $client_id Spotify api credential
    * @param $client_secret Spotify api credential
    * @param $code Code returned after Spotify authentification. This code is
    * used to get access token.
    * @return Object instance
    */
    public static function getInstance($client_id, $client_secret, $code = false) {
        if (self::$instance === false) {
            self::$instance = new \App\Oauth($client_id, $client_secret, $code);
        }
        return self::$instance;
    }

    /**
    * Get access token from the code returned by Spotify authentication.
    * @param $code Code returned by Spotify authentication
    */
    public function login($code)
    {
        $this->getAccessToken($code);
    }

    /**
    * Check if user already auth on Spotify based on weather or not a refresh
    * token is set.
    * @return true if user is authenticated, else false
    */
    public function isAuth()
    {
        if ($this->refresh_token) {
            return true;
        }
        return false;
    }

    /**
    * Clear parameters store in $_SESSION.
    */
    public static function logOut()
    {
        \App\Session::set('access_token', false);
        \App\Session::set('refresh_token', false);
        \App\Session::set('expire_date', false);
    }

    /**
    * Build URL where the user must be redirect to retrieve a code. User will
    * have to login then will be redirect to $redirect_uri where the code will
    * be used to get access_token.
    * The access_token is the token you have to use to every spotify's api call.
    * @return URL where user can login his Spotify account
    */
    public function getLoginUrl()
    {
        if ($this->client_id === false) {
            throw new \Exception("Missing spotify client_id.", self::SPOTIFY_AUTH_ERROR);
        }
        $scopeArray = array(
            'playlist-read-private',
            'playlist-read-collaborative',
            'playlist-modify-public',
            'playlist-modify-private',
            'user-read-private',
            'user-read-birthdate',
            'user-read-email'
        );
        $scopes = implode(' ', $scopeArray);

        $url = 'https://accounts.spotify.com/authorize';

        $param = array(
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'scope' => urlencode($scopes),
            'redirect_uri' => urlencode($this->redirect_uri),
            'show_dialog' => 'true',
        );
        $data = array();
        foreach ($param as $key => $value) {
            $data[] = $key.'='.$value;
        }
        return $url.'?'.implode('&', $data);
    }

    /**
    * If a valid token is set, return it, else refresh it.
    * @return Valid Spotify API access token
    */
    public function getToken()
    {
        if ($this->access_token && $this->isTokenExpired() === false) {
            return $this->access_token;
        } else if ($this->refresh_token) {
            return $this->refreshToken();
        } else {
            throw new \Exception("Authentication error, please log in again.", self::OAUTH_AUTH_ERROR);
        }
    }

    /**
    * Get the first access and refresh token from the code returned after Spotify
    * login and sotre it in the current object.
    * @param $code Code returned by Spotify authentication
    */
    private function getAccessToken($code)
    {
        $data = \App\Curl::post('https://accounts.spotify.com/api/token', array(
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => urlencode($this->redirect_uri),
        ), array(
            'Authorization' => 'Basic '.base64_encode($this->client_id.':'.$this->client_secret),
        ));
        $parsed_data = $this->processToken($data);
        if (isset($parsed_data->refresh_token)) {
            \App\Session::set('refresh_token', $parsed_data->refresh_token);
            $this->refresh_token = $parsed_data->refresh_token;
        }
    }

    /**
    * Get an access token from a refresh token and sotre it in the current object.
    */
    private function refreshToken()
    {
        $data = \App\Curl::post('https://accounts.spotify.com/api/token', array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refresh_token,
        ), array(
            'Authorization' => 'Basic '.base64_encode($this->client_id.':'.$this->client_secret),
        ));
        $this->processToken($data);
    }

    /**
    * Store access token, and his DateTime of the expiration from the RPC to
    * Spotify API.
    * @param $data Data as string (json) from RPC to Spotify API
    * @return Object from $data parsing
    */
    private function processToken($data)
    {
        $parsed_data = json_decode($data);
        if (isset($parsed_data->access_token)
            && isset($parsed_data->expires_in)) {

                $expire_date = new \DateTime();
                $expire_date->modify('+ '.$parsed_data->expires_in.' seconds');

                \App\Session::set('access_token', $parsed_data->access_token);
                \App\Session::set('expire_date', $expire_date->format('Y-m-d H:i:s'));

                $this->access_token = $parsed_data->access_token;
        } else {
            throw new \Exception("Error while log you in.", self::OAUTH_AUTH_ERROR);
        }
        return $parsed_data;
    }

    /**
    * Check if the access token still valid based on the expires_in value returned
    * when getting it from Spotify API.
    * @return true if access token is expired, false else
    */
    private function isTokenExpired()
    {
        if ($this->expire_date === false) {
            return true;
        }
        $now = new \DateTime();
        return ($now > $this->expire_date);
    }
}
