<?php
namespace App\Controllers;

use App\Models\User;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuariosController
{
    private $codesError = [
        1 => 404,
        2 => 409,
        'default' => 400
    ];

    public function login(Request $request, Response $response)
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            $user = User::where('email', $data['email'])
                ->where('password', $data['password'])
                ->first();

            if (empty($user)) {
                throw new Exception("Credenciales incorrectas", 1);
            }

            // Generar token aleatorio
            $token = bin2hex(random_bytes(32));
            $user->token = $token;
            $user->save();

            $response->getBody()->write($user->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            $response->getBody()->write(json_encode([
                'error' => $ex->getMessage()
            ]));
            return $response
                ->withStatus($status)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public function logout(Request $request, Response $response)
    {
        try {
            $headers = $request->getHeader('Authorization');
            $token = $headers[0] ?? null;

            if ($token && strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $user = User::where('token', $token)->first();

            if (empty($user)) {
                throw new Exception("Token invalido", 1);
            }

            $user->token = null;
            $user->save();

            $response->getBody()->write(json_encode([
                'message' => 'Sesion cerrada correctamente'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            return $response->withStatus($status);
        }
    }

    public function register(Request $request, Response $response)
    {
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->role = $data['role'] ?? 'gestor';
            $user->save();

            $response->getBody()->write($user->toJson());
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            return $response->withStatus($status);
        }
    }

    public function queryAllUsers(Request $request, Response $response)
    {
        try {
            $users = User::all();
            
            if (count($users) == 0) {
                return $response->withStatus(204);
            }

            $response->getBody()->write($users->toJson());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            return $response->withStatus($status);
        }
    }

    public function getUserById(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];

            if (empty($id)) {
                throw new Exception("ID requerido", 2);
            }

            $user = User::find($id);
            
            if (empty($user)) {
                throw new Exception("Usuario no encontrado", 1);
            }

            $response->getBody()->write($user->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            return $response->withStatus($status);
        }
    }

    public function updateUser(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            $user = User::find($id);

            if (empty($user)) {
                throw new Exception("Usuario no encontrado", 1);
            }

            if (isset($data['name'])) $user->name = $data['name'];
            if (isset($data['email'])) $user->email = $data['email'];
            if (isset($data['password'])) $user->password = $data['password'];
            if (isset($data['role'])) $user->role = $data['role'];

            $user->save();

            $response->getBody()->write($user->toJson());
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            return $response->withStatus($status);
        }
    }

    public function deleteUser(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];

            if (empty($id)) {
                throw new Exception("ID requerido", 2);
            }

            $user = User::find($id);
            
            if (empty($user)) {
                throw new Exception("Usuario no encontrado", 1);
            }

            if (!$user->delete()) {
                throw new Exception("Error al eliminar", 3);
            }

            return $response
                ->withStatus(204)
                ->withHeader('Content-Type', 'application/json');
        } catch (Exception $ex) {
            $status = $this->codesError[$ex->getCode()] ?? $this->codesError['default'];
            return $response->withStatus($status);
        }
    }
}