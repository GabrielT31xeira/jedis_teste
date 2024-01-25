<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    # Função de login
    public function login(Request $request) : JsonResponse
    {
        try {
            # Validação dos dados enviados
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $data = $request->all();
            # Verificando se os dados existem no banco de dados
            if (Auth::attempt($data)) {
                # Pegando o usuario autenticado
                $user = Auth::user();
                # Gerando e retornando o token para o usuarios
                $token = $user->createToken('JedisAPI')->accessToken;
                return response()->json(['token' => $token], 200);
            } else {
                # Caso os dados não sejam encontrados retorna não autorizado
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        # Retorno de erros relacionados ao servidor
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    # Cadastro de usuario
    public function register(Request $request)
    {
        try {
            # Validação dos dados enviados
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]);

            # Criação do usuario de acordo com os dados enviados
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            # Gerando e retornando o token para o usuarios
            $token = $user->createToken('JedisAPI')->accessToken;
            return response()->json([
                'message' => 'User created successfully',
                'token' => $token
            ]);
        # Retorno de erros relacionados ao servidor
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            # Checa a existencia do token
            if(Auth::guard('api')->check()){
                # Busca o token no banco de dados
                $accessToken = Auth::guard('api')->user()->token();
                    # Revoga o token
                    DB::table('oauth_refresh_tokens')
                        ->where('access_token_id', $accessToken->id)
                        ->update(['revoked' => true]);
                $accessToken->revoke();
                #retorna mensagem de sucesso
                return response()->json([
                    'message' => 'User logout successfully.'
                ],200);
            } else {
                # Caso o token não seja encontrado
                return response()->json([
                    'message' => 'Token não encontrado'
                ],404);
            }
        # Retorno de erros relacionados ao servidor
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile() {
        try {
            # Busca os dados do usuario logado
            $user = Auth::guard('api')->user();
            return response()->json([
                'user' => $user
            ],200);
        # Retorno de erros relacionados ao servidor
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error has occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
