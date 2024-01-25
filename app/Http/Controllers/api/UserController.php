<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        try {
            # Busca todos os usuarios no banco de dados
            $users = User::all();
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
            # Busca usuario no banco de dados
            $user = User::find($id);
            # Se não encontrar o usuario retorna um not found
            if($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
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
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]);

            # Busca usuario no banco de dados
            $user = User::find($id);

            # Se não encontrar o usuario retorna um not found
            if($user == null) {
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
            if($user == null) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }
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
