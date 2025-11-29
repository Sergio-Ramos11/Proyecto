<?php

use Psr\Http\Message\ResponseInterface as Response;

function jsonResponse(Response $response, array $data, int $status = 200)
{
    $response->getBody()->write(json_encode($data));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}