<?php

namespace App\Middleware;

use App\Models\Usuarios;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');


        if (!$header || !str_starts_with($header, 'Bearer ')) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(["error" => "Token no enviado"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }


        $token = str_replace('Bearer ', '', $header);


        $user = Usuarios::where('token', $token)->first();

        if (!$user) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(["error" => "Token inválido"]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }


        $request = $request->withAttribute('user', $user);


        return $handler->handle($request);
    }
}


