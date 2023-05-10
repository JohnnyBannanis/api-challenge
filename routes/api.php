<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ComercioController;
use App\Http\Controllers\API\DispositivoController;
use App\Http\Controllers\API\VentaController;
use App\Http\Controllers\API\UsuarioController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource( name:'comercios', controller: ComercioController::class);
Route::apiResource( name:'dispositivos', controller: DispositivoController::class);
Route::post('/login', [DispositivoController::class, 'logInUsuario']);
Route::post('/logout', [DispositivoController::class, 'logOutUsuario']);
Route::apiResource( name:'ventas', controller: VentaController::class);
Route::post('/anular', [VentaController::class, 'anular']);
Route::apiResource( name:'usuarios', controller: UsuarioController::class);
