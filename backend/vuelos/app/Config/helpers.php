<?php

function jsonResponse($response, $data, $status = 200)
{
    $response->getBody()->write(json_encode($data));
    return $response->withStatus($status)
    ->withHeader('Content-Type', 'application/json');
}
