<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\MovimientoEquipoController;

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

// Rutas para Funcionarios
Route::prefix('funcionarios')->group(function () {
    Route::get('/', [FuncionarioController::class, 'index']);
    Route::post('/', [FuncionarioController::class, 'store']);
    Route::get('/{id}', [FuncionarioController::class, 'show']);
    Route::put('/{id}', [FuncionarioController::class, 'update']);
    Route::delete('/{id}', [FuncionarioController::class, 'destroy']);
});

// Rutas para Equipos
Route::prefix('equipos')->group(function () {
    Route::get('/', [EquipoController::class, 'index']);
    Route::post('/', [EquipoController::class, 'store']);
    Route::get('/{id}', [EquipoController::class, 'show']);
    Route::put('/{id}', [EquipoController::class, 'update']);
    Route::patch('/{id}/status', [EquipoController::class, 'updateStatus']);
    Route::delete('/{id}', [EquipoController::class, 'destroy']);
});

// Rutas para Movimientos
Route::prefix('movimientos')->group(function () {
    Route::get('/', [MovimientoEquipoController::class, 'index']);
    Route::post('/', [MovimientoEquipoController::class, 'store']);
    Route::get('/{id}', [MovimientoEquipoController::class, 'show']);
    Route::get('/equipo/{equipoId}', [MovimientoEquipoController::class, 'getByEquipo']);
    Route::get('/funcionario/{funcionarioId}', [MovimientoEquipoController::class, 'getByFuncionario']);
});
