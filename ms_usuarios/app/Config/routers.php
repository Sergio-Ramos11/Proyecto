<?php
use App\Controllers\UsuariosController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$tokenValidation = require __DIR__ . '/../Middleware/TokenValidation.php';

return function (App $app) use ($tokenValidation) {
    
    // Ruta de prueba
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Microservicio de Usuarios - Gestión de Vuelos");
        return $response;
    });

    $app->post('/login', [UsuariosController::class, 'login']);
    

    $app->post('/register', [UsuariosController::class, 'register']);

    
    $app->group('/usuarios', function (RouteCollectorProxy $group) {

        $group->get('', [UsuariosController::class, 'queryAllUsers']);
        
        $group->get('/{id}', [UsuariosController::class, 'getUserById']);
        
        $group->put('/{id}', [UsuariosController::class, 'updateUser']);
        
        $group->delete('/{id}', [UsuariosController::class, 'deleteUser']);
        
    })->add($tokenValidation);

    $app->post('/logout', [UsuariosController::class, 'logout'])
        ->add($tokenValidation);
};