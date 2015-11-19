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
        \App\Session::set('spotify_code', $app->request->get('code'));
        $app->setCookie('spotify_code', $app->request->get('code'));
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
* Check if a code is available in session or cookie
*/
$app->get('/api/islogin', function () use ($app) {
    $data = array('is_login' => false);
    if (\App\Session::get('spotify_code')) {
        $data['is_login'] = true;
    } else if ($app->getCookie('spotify_code', false)) {
        \App\Session::set('spotify_code', $app->getCookie('spotify_code'));
        $data['is_login'] = true;
    }
    \App\Response::json_response($data);
});

/**
* Clear session and cookies
*/
$app->get('/api/logout', function () use ($app) {
    \App\Session::set('spotify_code', false);
    $app->deleteCookie('spotify_code');
});

/**
* Get Spotify login page URL with correct scope.
*/
$app->get('/api/get_authentification_url', function() {
    $client_id = \App\Config::get('spotify.client_id');
    $redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].'/authentification/callback';
    $scopeArray = array(
        'playlist-read-private',
        'playlist-read-collaborative',
        'playlist-modify-public',
        'playlist-modify-private'
    );
    $scopes = implode(' ', $scopeArray);
    $url = \App\Oauth::getAuthorizeUrl($client_id, $redirect_uri, $scopes);
    \App\Response::json_response(array('url' => $url));
});

$app->run();

?>
