<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

# Rotas relacionadas ao login
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    # CRUD DE USUARIOS
    Route::get('users', [UserController::class,'index']);
    Route::post('user', [UserController::class,'store']);
    Route::get('user/{id}', [UserController::class,'show']);
    Route::put('user/{id}', [UserController::class,'update']);
    Route::delete('user/{id}', [UserController::class,'destroy']);

    # CRUD DE PRODUTOS
    Route::get('products', [ProductController::class,'index']);
    Route::post('product', [ProductController::class,'store']);
    Route::get('product/{id}', [ProductController::class,'show']);
    Route::put('product/{id}', [ProductController::class,'update']);
    Route::delete('product/{id}', [ProductController::class,'destroy']);
});
