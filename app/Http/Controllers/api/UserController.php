<?php

namespace App\Http\Controllers\api;

use App\Helpers\Cacher;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $cacher;

    public function __construct()
    {
        $this->cacher = new Cacher('file');
    }
    public function index()
    {
        try {
            # Busca os dados no redis
            $cacherData = $this->cacher->getCached('user');
            # Atualiza a variavel para o retorno rápido
            if ($cacherData) {
                $users = $cacherData;
            } else {
                # Busca todos os usuarios no banco de dados
                $users = User::all();

                if ($users->count() == 0) {
                    return response()->json([
                        "message" => "No Users Found",
                    ], 404);
                }
                $this->cacher->setCached('user_',  $users->toJson());
            }
            return response()->json([
                'message' => 'User list',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            # Retorno de erros relacionados ao servidor
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            # Validação dos dados enviados
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'error' => $validate->errors()
                ], 422);
            }

            # Criação do usuario de acordo com os dados enviados
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            # Retorno de erros relacionados ao servidor
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            # Busca os dados no redis
            $cacherData = $this->cacher->getCached('user');
            # Atualiza a variavel para o retorno rápido
            if ($cacherData) {
                $user = $cacherData;
                # Busca todos os usuarios no banco de dados
            } else {
                # Busca usuario no banco de dados
                $user = User::find($id);
                # Se não encontrar o usuario retorna um not found
                if ($user == null) {
                    return response()->json([
                        'message' => 'User not found'
                    ], 404);
                }
                # Adiciona valores ao redis
                $this->cacher->setCached('user_'.$id,  $user->toJson());
            }
            # Retorna o usuario
            return response()->json([
                'message' => 'User find',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            # Retorno de erros relacionados ao servidor
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            # Validação dos dados enviados
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'error' => $validate->errors()
                ], 422);
            }

            # Busca usuario no banco de dados
            $user = User::find($id);

            # Se não encontrar o usuario retorna um not found
            if ($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            # Altera e salva os dados no banco de dados
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'message' => 'User update successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            # Retorno de erros relacionados ao servidor
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            # Busca usuario no banco de dados
            $user = User::find($id);
            # Se não encontrar o usuario retorna um not found
            if ($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            # Apaga os produtos que aquele usuario cadastrou
            $user->creator()->delete();
            # Apaga os produtos que aquele usuario atualizou
            $user->updater()->delete();
            # Apaga usuario do banco
            $user->delete();

            # Retorna mensagem de sucesso
            return response()->json([
                'message' => 'User delete successfully'
            ]);
        } catch (\Exception $e) {
            # Retorno de erros relacionados ao servidor
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
