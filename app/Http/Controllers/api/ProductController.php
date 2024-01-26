<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        try {
            # Busca os dados no redis
            $cacherData = Redis::get('product');
            # Atualiza a variavel para o retorno rápido
            if ($cacherData) {
                $products = json_decode($cacherData, true);
                # Busca todos os produtos no banco de dados
            } else {
                $products = Product::with('creator', 'updater')->get();
                if ($products->count() == 0) {
                    return response()->json([
                        "message" => "No Products Found",
                    ], 404);
                }
                # Insere os dados no redis
                Redis::set('product', $products->toJson());
                Redis::expire('product', 60);
            }
            return response()->json([
                'message' => 'Product list',
                'products' => $products
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
                'description' => 'required|string|max:255',
                'preco' => 'required|numeric',
                'garantia' => 'required|string|max:255',
                'marca' => 'required|string|max:255',
                'material' => 'required|string|max:255',
                'origem' => 'required|string|max:255'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'error' => $validate->errors()
                ], 422);
            }
            # Buscando o usuario logado
            $user_id = Auth::user()->id;

            # Criação do produto de acordo com os dados enviados
            $product = Product::create([
                'created_by' => $user_id,
                'name' => $request->name,
                'description' => $request->description,
                'preco' => $request->preco,
                'garantia' => $request->garantia,
                'marca' => $request->marca,
                'material' => $request->material,
                'origem' => $request->origem
            ]);

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product
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
            $cacherData = Redis::get('product' . $id);
            # Atualiza a variavel para o retorno rápido
            if ($cacherData) {
                $product = json_decode($cacherData, true);
            } else {
                # Busca o produto no banco de dados
                $product = Product::with('creator', 'updater')->find($id);
                # Se não encontrar o produto retorna um not found
                if ($product == null) {
                    return response()->json([
                        'message' => 'Product not found'
                    ], 404);
                }
                Redis::set('product' . $id, $product->toJson());
                Redis::expire('product' . $id, 60);
            }

            # Retorna o produto
            return response()->json([
                'message' => 'Product find',
                'product' => $product
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
            $validate = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'description' => 'string|max:255',
                'preco' => 'numeric',
                'garantia' => 'string|max:255',
                'marca' => 'string|max:255',
                'material' => 'string|max:255',
                'origem' => 'string|max:255'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'error' => $validate->errors()
                ], 422);
            }

            # Busca o produto no banco de dados
            $product = Product::find($id);

            # Se não encontrar o produto retorna um not found
            if ($product == null) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            # Buscando o usuario logado
            $user_id = Auth::user()->id;

            # Altera e salva os dados no banco de dados
            $product->name = $request->name;
            $product->description = $request->description;
            $product->preco = $request->preco;
            $product->garantia = $request->garantia;
            $product->marca = $request->marca;
            $product->material = $request->material;
            $product->origem = $request->origem;
            $product->updated_by = $user_id;
            $product->save();

            # Busca o produto no banco de dados
            $product = Product::with('creator', 'updater')->find($id);

            return response()->json([
                'message' => 'Product update successfully',
                'product' => $product
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
            # Busca o produto no banco de dados
            $product = Product::find($id);
            # Se não encontrar o produto retorna um not found
            if ($product == null) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }
            # Apaga o dado do redis
            Redis::del('product' . $id);
            # Apaga o produto do banco
            $product->delete();

            # Retorna mensagem de sucesso
            return response()->json([
                'message' => 'Product delete successfully'
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
