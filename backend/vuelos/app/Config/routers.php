<?php

use App\Controllers\NavesController;
use App\Controllers\VuelosController;
use App\Controllers\ReservasController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function($app){


    $app->group('/naves', function(RouteCollectorProxy $group){

        $group->get('/all', [NavesController::class, 'all']);
        $group->post('/create', [NavesController::class, 'create']);
        $group->put('/update/{id}', [NavesController::class, 'update']);
        $group->delete('/delete/{id}', [NavesController::class, 'delete']);

    });


    $app->group('/vuelos', function(RouteCollectorProxy $group){

        $group->get('/all', [VuelosController::class, 'all']);
        $group->post('/create', [VuelosController::class, 'create']);
        $group->put('/update/{id}', [VuelosController::class, 'update']);
        $group->delete('/delete/{id}', [VuelosController::class, 'delete']);


        $group->get('/search', [VuelosController::class, 'search']);
    });


    $app->group('/reservas', function(RouteCollectorProxy $group){


        $group->post('/create', [ReservasController::class, 'create'])
              ->add(new AuthMiddleware());


        $group->get('/my', [ReservasController::class, 'my'])
              ->add(new AuthMiddleware());


        $group->delete('/delete/{id}', [ReservasController::class, 'delete'])
              ->add(new AuthMiddleware());
    });
};
