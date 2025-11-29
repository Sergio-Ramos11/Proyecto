<?php
namespace App\Controllers;

use App\Models\Nave;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class NavesController
{
    public function all(Request $request, Response $response)
    {
        $rows = Nave::all();
        return jsonResponse($response, $rows);
    }

    public function create(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!isset($data['name'], $data['capacity'], $data['model'])) {
            return jsonResponse($response, ["error" => "Todos los campos son requeridos"], 400);
        }

        $nave = Nave::create([
            'name' => $data['name'],
            'capacity' => (int)$data['capacity'],
            'model' => $data['model']
        ]);

        return jsonResponse($response, [
            "message" => "Nave creada correctamente",
            "nave" => $nave
        ], 201);
    }

    public function update(Request $request, Response $response, array $args)
    {
        $id = (int)$args['id'];
        $nave = Nave::find($id);

        if (!$nave)
            return jsonResponse($response, ["error" => "Nave no encontrada"], 404);

        $data = $request->getParsedBody();
        $nave->fill($data);
        $nave->save();

        return jsonResponse($response, ["message" => "Nave actualizada correctamente"]);
    }

    public function delete(Request $request, Response $response, array $args)
    {
        $id = (int) $args['id'];
        $nave = Nave::find($id);

        if (!$nave)
            return jsonResponse($response, ["error" => "Nave no encontrada"], 404);

        $nave->delete();
        return jsonResponse($response, ["message" => "Nave eliminada correctamente"]);
    }
}

