<?php

require '../vendor/autoload.php';

\App\Session::init();

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Smarty()
));

$app->config(array(
    'templates.path' => '../app/templates/'
));

$view = $app->view();
$view->parserCompileDirectory = dirname(__FILE__) . '/../template_compiled';

/**
* Show application main page
*/
$app->get('/', function () use ($app) {
    $app->render('index.tpl');
});

/**
* Display when user unregister from the service
*/
$app->get('/bye', function () use ($app) {
    $app->render('bye.tpl');
});

/**
* Page where the User is redirected after login on spotify service. It retrieve
* and store a code used to get an access token to spotify. As the login page
* is open in a popup, the page close the popup and move the app interface
* to the next step.
*/
$app->get('/authentification/callback', function() use ($app) {
    if ($app->request->get('code', false)) {
        try {
            \App\Oauth::getInstance(
                \App\Config::get('spotify.client_id'),
                \App\Config::get('spotify.client_secret')
            )->login($app->request->get('code'));
        } catch (\Exception $e) {
            \App\Session::init();
            $app->flash('error', $e->getMessage());
        }
    } else {
        \App\Session::init();
        $error = $app->request->get('error', false);
        if ($error && $error == 'access_denied') {
            $app->flash('error', 'Please grant access to your Spotify Account.');
        } else {
            $app->flash('error', 'Oops, an unknown error occured.');
        }
    }
    $app->redirect('/');
});

/**
* Clear session
*/
$app->get('/api/logout', function () use ($app) {
    \App\Oauth::logOut();
});

/**
* Check if a spotify code is available in session
*/
$app->get('/api/is_auth', function () use ($app) {
    $data = array('is_auth' => \App\Oauth::getInstance(
        \App\Config::get('spotify.client_id'),
        \App\Config::get('spotify.client_secret')
    )->isAuth());
    \App\Response::json_response($data);
});

/**
* Get Spotify login page URL with correct scope.
*/
$app->get('/api/get_authentification_url', function() {
    $oAuth = \App\Oauth::getInstance(
        \App\Config::get('spotify.client_id'),
        \App\Config::get('spotify.client_secret')
    );
    try {
        \App\Response::json_response(array(
            'error' => 0,
            'url' => $oAuth->getLoginUrl()
        ));
    } catch (\Exception $e) {
        \App\Response::json_response(array(
            'error' => $e->getCode(),
            'message' => $e->getMessage(),
        ));
    }
});

/**
* Return the Spotify user profile currently authenticated.
*/
$app->get('/api/get_user', function () use($app) {
    $return = array();
    try {
        $spotify = new \App\Spotify(
            \App\Config::get('spotify.client_id'),
            \App\Config::get('spotify.client_secret')
        );
        $return = array(
            'error' => 0,
            'user' => $spotify->getUserProfil()
        );
    } catch (\Exception $e) {
        $return = array(
            'error' => $e->getCode(),
            'message' => $e->getMessage(),
        );
    }
    \App\Response::json_response($return);
});

/**
* Retrieve spotify playlists for a lgged in account.
*/
$app->get('/api/get_playlist', function () use ($app) {
    $return = array();
    try {
        $spotify = new \App\Spotify(
            \App\Config::get('spotify.client_id'),
            \App\Config::get('spotify.client_secret')
        );
        $return = array(
            'error' => 0,
            'playlist' => $spotify->getPlaylistList()
        );
        \App\Response::json_response($return);
    } catch (\Exception $e) {
        $return = array(
            'error' => $e->getCode(),
            'message' => $e->getMessage(),
        );
        \App\Response::json_response($return);
    }
});

/**
* Get current authenticated user's Discover Playlist from Spotify.
*/
$app->get('/api/find_discover_playlist', function () use ($app) {
    try {
        $spotify = new \App\Spotify(
            \App\Config::get('spotify.client_id'),
            \App\Config::get('spotify.client_secret')
        );
        $playlist = $spotify->getDiscoverPlaylist();
        $return = array(
            'error' => 0,
            'playlist_id' => $playlist->id
        );
        \App\Response::json_response($return);
    } catch (\Exception $e) {
        $return = array(
            'error' => $e->getCode(),
            'message' => $e->getMessage(),
        );
        \App\Response::json_response($return);
    }
});

/**
* Create a playlist into the currently authenticated Spotify user profile.
*/
$app->get('/api/create_playlist', function () use ($app) {
    try {
        $now = new \DateTime();
        $playlist_name = 'Spoty week '.$now->format('Y-W');

        $spotify = new \App\Spotify(
            \App\Config::get('spotify.client_id'),
            \App\Config::get('spotify.client_secret')
        );
        $playlist = $spotify->createPlaylist($playlist_name);
        $return = array(
            'error' => 0,
            'playlist' => $playlist
        );
        \App\Response::json_response($return);
    } catch (\Exception $e) {
        $return = array(
            'error' => $e->getCode(),
            'message' => $e->getMessage(),
        );
        \App\Response::json_response($return);
    }
});

/**
* Copy tracks from a playlist to another one.
*/
$app->get('/api/copy_playlist', function () use ($app) {
    try {
        $src_playlist_id = $app->request->get('src_playlist_id');
        $dest_playlist_id = $app->request->get('dest_playlist_id');
        $spotify = new \App\Spotify(
            \App\Config::get('spotify.client_id'),
            \App\Config::get('spotify.client_secret')
        );
        $result = $spotify->copyPlaylist($src_playlist_id, $dest_playlist_id);

        if ($result === true) {
            $return = array(
                'error' => 0
            );
        } else {
            $return = array(
                'error' => \App\Spotify::SPOTIFY_DISCOVER_PLAYLIST_COPY_WENT_WRONG
            );
        }

        \App\Response::json_response($return);
    } catch (\Exception $e) {
        $return = array(
            'error' => $e->getCode(),
            'message' => $e->getMessage(),
        );
        \App\Response::json_response($return);
    }

});

$app->run();

?>
