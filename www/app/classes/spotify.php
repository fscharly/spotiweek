<?php

/**
* Everything about Spotify API can be found at
* https://developer.spotify.com/web-api/
*/

namespace App;

class Spotify
{
    private $client_id = false;
    private $client_secret = false;
    private $access_token = false;
    private $user_profil = false;

    const SPOTIFY_AUTH_ERROR = 1;
    const SPOTIFY_DISCOVER_PLAYLIST_NOT_FOUND = 2;
    const SPOTIFY_PLAYLIST_ALREADY_EXSITS = 3;
    const SPOTIFY_EXPECTED_DATA_NOT_FOUND = 4;
    const SPOTIFY_DISCOVER_PLAYLIST_TRACK_CANT_BE_FOUND = 5;
    const SPOTIFY_DISCOVER_PLAYLIST_COPY_WENT_WRONG = 6;

    public function __construct($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    /**
    * Check if Spotify object is well constructed then ask Oauth class for
    * an access token. It's the Oauth class that checks if the token need to
    * be refreshed.
    * @return Valid Spotify API access token
    */
    private function getToken()
    {
        if ($this->client_id === false) {
            throw new \Exception("Missing spotify client_id.", self::SPOTIFY_AUTH_ERROR);
        }
        if ($this->client_secret === false) {
            throw new \Exception("Missing spotify client_secret.", self::SPOTIFY_AUTH_ERROR);
        }
        $oAuth = \App\Oauth::getInstance(
            $this->client_id,
            $this->client_secret
        );
        return $oAuth->getToken();
    }

    /**
    * Return information about Spotify User. Spotify User id is used in almost
    * every API call, so it's stored in the object to avoid multiple RPC.
    * @return Spotify user profile
    */
    public function getUserProfil()
    {
        if ($this->user_profil == false) {
            $token = $this->getToken();
            $data = \App\Curl::get('https://api.spotify.com/v1/me', false, array(
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json'
            ));
            $parsed_data = json_decode($data);
            if (isset($parsed_data->error)) {
                throw new \Exception($parsed_data->error->message, $parsed_data->error->status);
            }
            $this->user_profil = $parsed_data;
        }
        return $this->user_profil;
    }

    /**
    * Get the playlist linked to the current authenticated Spotify User.
    * @return List of Spotify playlist
    */
    public function getPlaylistList()
    {
        $user = $this->getUserProfil();
        $token = $this->getToken();
        $data = \App\Curl::get('https://api.spotify.com/v1/users/'.$user->id.'/playlists', false, array(
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ));
        $parsed_data = json_decode($data);
        if (isset($parsed_data->error)) {
            throw new \Exception($parsed_data->error->message, $parsed_data->error->status);
        }
        return $parsed_data;
    }

    /**
    * Get the Spotify Discover playist based on the owner id (spotifydiscover).
    * @return Spotify discover playlist
    */
    public function getDiscoverPlaylist()
    {
        $playlistList = $this->getPlaylistList();
        foreach ($playlistList->items as $playlist) {
            if ($playlist->owner->id == 'spotifydiscover') {
                return $playlist;
            }
        }
        throw new \Exception("Spotify Discover playlist not found.", self::SPOTIFY_DISCOVER_PLAYLIST_NOT_FOUND);
    }

    /**
    * Get all playlists from the current authenticated Spotify User then return
    * the first one that have a field ($key) valued at $value.
    * @param $key Field to evaluate
    * @param $value Value to find
    * @return First Spotify playist that match
    */
    public function findPlaylistBy($key, $value)
    {
        $playlistList = $this->getPlaylistList();
        foreach ($playlistList->items as $playlist) {
            if ($playlist->$key == $value) {
                return $playlist;
            }
        }
        return false;
    }

    /**
    * Create a new playlist in the current authenticated Spotify User account,
    * then return it.
    * @param $playlist_name Name of the Spotify playlist to create
    * @return Object Spotify created playlist
    */
    public function createPlaylist($playlist_name)
    {
        if ($this->findPlaylistBy('name', $playlist_name)) {
            throw new \Exception(sprintf("A playlist named %s already exists.", $playlist_name), self::SPOTIFY_PLAYLIST_ALREADY_EXSITS);
        }
        $user = $this->getUserProfil();
        $token = $this->getToken();
        $data_to_send = array(
            'name' => $playlist_name,
            'public' => false
        );
        $parsed_data_to_send = json_encode($data_to_send);
        $data = \App\Curl::post('https://api.spotify.com/v1/users/'.$user->id.'/playlists', $parsed_data_to_send, array(
            'Authorization' => 'Bearer '.$token,
            'Content-Type' => 'application/json',
        ));
        $parsed_data = json_decode($data);
        if (isset($parsed_data->error)) {
            throw new \Exception($parsed_data->error->message, $parsed_data->error->status);
        }
        return $parsed_data;
    }

    /**
    * Copy all tacks in the playlist identified by $src_playlist_id in the
    * playlist $dest_playlist_id playlist.
    * @param $src_playlist_id Spotify playslit id where tracks are picked
    * @param $dest_playlist_id Spotify playslit id where tracks are copied
    * @param true if the copy is successfull, false else
    */
    public function copyPlaylist($src_playlist_id, $dest_playlist_id)
    {
        $user = $this->getUserProfil();
        $token = $this->getToken();

        $src_playlist = $this->findPlaylistBy('id', $src_playlist_id);
        if (!$src_playlist) {
            throw new \Exception("Spotify Discover playlist not found.", self::SPOTIFY_DISCOVER_PLAYLIST_NOT_FOUND);
        }

        $trackList = $this->getPlaylistTracks($src_playlist);
        $urlTrackList = array();
        foreach ($trackList as $track) {
            $urlTrackList[] = $track->track->uri;
        }

        if (count($urlTrackList) <= 0) {
            throw new \Exception("Any tarcks has been found in Spotify discover playlist.", self::SPOTIFY_DISCOVER_PLAYLIST_TRACK_CANT_BE_FOUND);
        }

        $data_to_send = array(
            'uris' => $urlTrackList
        );
        $parsed_data_to_send = json_encode($data_to_send);

        $data = \App\Curl::post(
            'https://api.spotify.com/v1/users/'.$user->id.'/playlists/'.$dest_playlist_id.'/tracks',
            $parsed_data_to_send,
            array(
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
            )
        );

        $parsed_data = json_decode($data);
        if (isset($parsed_data->error)) {
            throw new \Exception($parsed_data->error->message, $parsed_data->error->status);
        }

        return true;
    }

    /**
    * Return the list of trakcs of the playlist $playlist.
    * @param $playlist Spotify playlist object
    * @return Array() List of Spotify tracks objects
    */
    public function getPlaylistTracks($playlist)
    {
        $token = $this->getToken();
        $data = \App\Curl::get('https://api.spotify.com/v1/users/'.$playlist->owner->id.'/playlists/'.$playlist->id.'/tracks', false, array(
            'Authorization' => 'Bearer '.$token,
        ));
        $parsed_data = json_decode($data);
        if (isset($parsed_data->error)) {
            throw new \Exception($parsed_data->error->message, $parsed_data->error->status);
        }
        if (!isset($parsed_data->items)) {
            throw new \Exception('Data returned by Spotify didn\'t have the expected format', self::SPOTIFY_EXPECTED_DATA_NOT_FOUND);
        }
        return $parsed_data->items;
    }
}
