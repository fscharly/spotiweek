<?php

require '../vendor/autoload.php';

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
* Page where the User is redirected after login on spotify service. It retrieve
* and store a code used to get an access token to spotify. As the login page
* is open in a popup, the page close the popup and move the app interface
* to the next step.
*/
$app->get('/authentification/callback', function() use ($app) {
    if ($app->request->get('code', false)) {
        \App\Session::set('spotify_code', $app->request->get('code'));
        $app->setCookie('spotify_code', $app->request->get('code'));
        $app->redirect('/');
    }
});

/**
* Check if a code is available in session or cookie
*/
$app->get('/api/isregister', function () use ($app) {
    $isRegister = false;

    if (\App\Session::get('spotify_code')) {
        $isRegister = true;
    } else if ($app->getCookie('spotify_code', false)) {
        \App\Session::set('spotify_code', $app->getCookie('spotify_code'));
        $isRegister = true;
    }
    \App\Response::json_response(array('is_register' => $isRegister));
});

/**
* Get Spotify login page URL with correct scope.
*/
$app->get('/api/get_authentification_url', function() {
    $client_id = \App\Config::get('api_token.client_id');
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
