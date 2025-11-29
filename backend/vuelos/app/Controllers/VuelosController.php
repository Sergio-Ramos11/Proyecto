<?php
namespace App\Controllers;

use App\Models\Vuelo;
use App\Models\Nave;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class VuelosController
{
    public function all(Request $request, Response $response)
    {
        $rows = Vuelo::with('nave')->get();
        return jsonResponse($response, $rows);
    }

    public function create(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!isset($data['origin'], $data['destination'], 
                  $data['departure'], $data['arrival'], 
                  $data['price'], $data['nave_id'])) {

            return jsonResponse($response, ["error" => "Todos los campos son requeridos"], 400);
        }

        if (!Nave::find($data['nave_id'])) {
            return jsonResponse($response, ["error" => "La nave no existe"], 404);
        }

        $vuelo = Vuelo::create($data);

        return jsonResponse($response, [
            "message" => "Vuelo creado correctamente",
            "vuelo" => $vuelo
        ], 201);
    }

    public function update(Request $request, Response $response, array $args)
    {
        $id = (int)$args['id'];
        $vuelo = Vuelo::find($id);

        if (!$vuelo)
            return jsonResponse($response, ["error" => "Vuelo no encontrado"], 404);

        $data = $request->getParsedBody();

        if (isset($data['nave_id']) && !Nave::find($data['nave_id'])) {
            return jsonResponse($response, ["error" => "La nave no existe"], 404);
        }

        $vuelo->fill($data);
        $vuelo->save();

        return jsonResponse($response, ["message" => "Vuelo actualizado correctamente"]);
    }

    public function delete(Request $request, Response $response, array $args)
    {
        $id = (int)$args['id'];
        $vuelo = Vuelo::find($id);

        if (!$vuelo)
            return jsonResponse($response, ["error" => "Vuelo no encontrado"], 404);

        $vuelo->delete();
        return jsonResponse($response, ["message" => "Vuelo eliminado correctamente"]);
    }

    public function search(Request $request, Response $response)
    {
        $params = $request->getQueryParams();

        $query = Vuelo::with('nave');

        if (!empty($params['origin'])) {
            $query->where('origin', 'LIKE', "%{$params['origin']}%");
        }

        if (!empty($params['destination'])) {
            $query->where('destination', 'LIKE', "%{$params['destination']}%");
        }

        if (!empty($params['date'])) {
            $query->whereDate('departure', $params['date']);
        }

        $rows = $query->get();

        return jsonResponse($response, $rows);
    }
}
