<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Config/database.php';

$cors = require __DIR__ . '/../app/Middleware/Cors.php';
$routers = require __DIR__ . '/../app/Config/routers.php';

$app = AppFactory::create();

$cors($app);
$routers($app);

$app->run();