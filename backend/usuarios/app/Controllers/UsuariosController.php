<?php

namespace App\Controllers;

use App\Models\Usuarios;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UsuariosController
{
    private function json(Response $response, $data, int $status = 200)
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function getUsuarios(Request $request, Response $response)
    {
        $rows = Usuarios::all();
        return $this->json($response, $rows);
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (empty($data['email']) || empty($data['password'])) {
            return $this->json($response, ['error' => 'Email y password requeridos'], 400);
        }

        $user = Usuarios::where('email', $data['email'])->first();

        if (!$user) {
            return $this->json($response, ['error' => 'Usuario no encontrado'], 404);
        }

        if ($data['password'] !== $user->password) {
            return $this->json($response, ['error' => 'Contraseña incorrecta'], 401);
        }

        if (!$user->token || $user->token === '') {
            $user->token = bin2hex(random_bytes(32));
            $user->save();
        }

        $token = $user->token;

        return $this->json($response, [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        $auth = $request->getHeaderLine('Authorization');

        if (!$auth) {
            return $this->json($response, ["error" => "Token requerido"], 401);
        }

    
        $token = str_replace('Bearer ', '', $auth);


        $user = Usuarios::where('token', $token)->first();

        if (!$user) {
            return $this->json($response, ["error" => "Token inválido"], 401);
        }

        if ($user->role !== 'administrador') {
            return $this->json($response, ["error" => "No autorizado"], 403);
        }

        if (!isset($data['name'], $data['email'], $data['password'], $data['role'])) {
            return $this->json($response, ["error" => "Todos los campos son requeridos"], 400);
        }

    
        if (Usuarios::where('email', $data['email'])->exists()) {
            return $this->json($response, ["error" => "El email ya está registrado"], 409);
        }


        if (!in_array($data['role'], ['administrador', 'gestor'])) {
            return $this->json($response, ["error" => "Rol inválido"], 400);
        }


        $nuevoUsuario = Usuarios::create([
            "name" => $data['name'],
            "email" => $data['email'],
            "password" => $data['password'],
            "role" => $data['role']
        ]);

        return $this->json($response, [
            "message" => "Usuario registrado correctamente",
            "user" => [
                "id" => $nuevoUsuario->id,
                "name" => $nuevoUsuario->name,
                "email" => $nuevoUsuario->email,
                "role" => $nuevoUsuario->role
            ]
        ], 201);
    }
    public function logout(Request $request, Response $response)
    {
        $auth = $request->getHeaderLine('Authorization');

        if (!$auth) {
            return $this->json($response, ["error" => "Token requerido"], 401);
        }

        $token = str_replace('Bearer ', '', $auth);

        $user = Usuarios::where('token', $token)->first();

        if (!$user) {
            return $this->json($response, ["error" => "Token inválido"], 401);
        }


        $user->token = null;
        $user->save();

        return $this->json($response, ["message" => "Sesión cerrada correctamente"]);
    }
    public function update(Request $request, Response $response, array $args)
    {
        $id = (int)$args['id'];
        $data = $request->getParsedBody();

        $user = Usuarios::find($id);

        if (!$user) {
            return $this->json($response, ["error" => "Usuario no encontrado"], 404);
        }

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            // Validar email único
            if (Usuarios::where('email', $data['email'])
                        ->where('id', '<>', $id)->exists()) {
                return $this->json($response, ["error" => "El email ya está registrado"], 409);
            }
            $user->email = $data['email'];
        }

        if (isset($data['password'])) {
            $user->password = $data['password'];
        }

        if (isset($data['role']) && in_array($data['role'], ['administrador', 'gestor'])) {
            $user->role = $data['role'];
        }

        $user->save();

        return $this->json($response, ["message" => "Usuario actualizado correctamente"]);
    }

    public function updateRole(Request $request, Response $response, array $args)
    {
        $id = (int)$args['id'];
        $data = $request->getParsedBody();

        if (!isset($data['role'])) {
            return $this->json($response, ["error" => "Rol requerido"], 400);
        }

        if (!in_array($data['role'], ['administrador', 'gestor'])) {
            return $this->json($response, ["error" => "Rol inválido"], 400);
        }

        $user = Usuarios::find($id);

        if (!$user) {
            return $this->json($response, ["error" => "Usuario no encontrado"], 404);
        }

        $user->role = $data['role'];
        $user->save();

        return $this->json($response, ["message" => "Rol actualizado correctamente"]);
    }

}