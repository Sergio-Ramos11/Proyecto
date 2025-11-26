<?php
use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;

return function (Request $request, $handler) {

    $headers = $request->getHeader('Authorization');
    $token = $headers[0] ?? null;


    if ($token && strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }

    $user = User::where('token', $token)->first();

    if (!$user) {
        $response = new Response();
        $response->getBody()->write(json_encode([
            'error' => 'Token inválido o sesión expirada'
        ]));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
};