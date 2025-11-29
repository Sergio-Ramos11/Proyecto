<?php

use App\Controllers\UsuariosController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

return function($app): void {

    $app->get('/', function(Request $request, Response $response){
        $response->getBody()->write("Hello world!");
        return $response;
    });

    
    $app->group('/usuarios', function(RouteCollectorProxy $group){


        $group->get('/all', [UsuariosController::class, 'getUsuarios'])
              ->add(new RoleMiddleware(['administrador']))
              ->add(new AuthMiddleware());


        $group->post('/login', [UsuariosController::class, 'login']);


        $group->post('/register', [UsuariosController::class, 'register'])
              ->add(new RoleMiddleware(['administrador']))
              ->add(new AuthMiddleware());

              
        $group->post('/logout', [UsuariosController::class, 'logout'])
              ->add(new AuthMiddleware());


        $group->put('/update/{id}', [UsuariosController::class, 'update'])
              ->add(new RoleMiddleware(['administrador']))
              ->add(new AuthMiddleware());


        $group->put('/role/{id}', [UsuariosController::class, 'updateRole'])
              ->add(new RoleMiddleware(['administrador']))
              ->add(new AuthMiddleware());

    });
};


