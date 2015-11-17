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

$app->get('/', function () use ($app) {
    //var_dump(\App\Config::get('hello'));
    $app->render('index.tpl');
});

$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

$app->run();

?>
