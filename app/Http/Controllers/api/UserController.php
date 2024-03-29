<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            # Busca os dados no redis
            $cacherData = Redis::get('user');
            # Atualiza a variavel para o retorno rápido
            if ($cacherData) {
                $users = json_decode($cacherData, true);
            } else {
                # Busca todos os usuarios no banco de dados
                $users = User::all();

                if ($users->count() == 0) {
                    return response()->json([
                        "message" => "No Users Found",
                    ], 404);
                }
                # Insere os dados no redis
                Redis::set('user', $users->toJson());
                Redis::expire('user', 60);
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
            $cacherData = Redis::get('user' . $id);
            # Atualiza a variavel para o retorno rápido
            if ($cacherData) {
                $user = json_decode($cacherData, true);
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
                Redis::set('user' . $id, $user->toJson());
                Redis::expire('user' . $id, 60);
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
            # Apaga o dado do redis
            Redis::del('user' . $id);
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
