<?php
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Config/data-BS.php';
require __DIR__ . '/../app/Config/helpers.php';

$routes = require __DIR__ . '/../app/Config/routers.php';


$app = AppFactory::create();


$app->addBodyParsingMiddleware();

$app->options('/{routes:.+}', function ($req, $res) {
    return $res;
});

$app->add(function ($req, $handler) {
    $res = $handler->handle($req);
    return $res
        ->withHeader("Access-Control-Allow-Origin", "*")
        ->withHeader("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, Accept, Origin, Authorization")
        ->withHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
        ->withHeader("Content-Type", "application/json");
});


$routes($app);


$app->run();
