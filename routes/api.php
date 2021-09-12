<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndicacaoController;
// use App\Http\Controllers\IndicacaoController;

/*
|__________________________________________________________________________|
|--------------------------------------------------------------------------|
|--------------------------------------------------------------------------|
| TESTE - LARAVEL - OTAVIO                                                 |
|--------------------------------------------------------------------------|
|--------------------------------------------------------------------------|
|__________________________________________________________________________|
*/

// Route::get('/login','AutenticarController@index')->name('login');

Route::fallback(function () {
    return response()->json(['error' => 'Rota inexistente!'], 404);
});

//Teste de middleware - Rota base
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Teste de middleware - Rota base	
Route::get('/use2r', function (Request $request) {
    return response()->json([ 'valid' => auth()->check() ]);
});

// Listar indicacoes
Route::middleware('auth:api')->get('/indicacoes', function (IndicacaoController $indicacoes) {
    return $indicacoes->index();
});

// Listar uma indicacao
Route::middleware('auth:api')->get('/indicacoes/{id}', function (IndicacaoController $indicacoes, Request $request) {
    return $indicacoes->show($request->id);
});

// Criar nova indicacao
Route::middleware('auth:api')->post('/indicacao', function (IndicacaoController $indicacoes, Request $request) {
    return $indicacoes->store($request);
});

// Deletar uma indicacao
Route::middleware('auth:api')->delete('/indicacao/{id}', function (IndicacaoController $indicacoes, Request $request) {
    return $indicacoes->destroy($request);
});

// Atera status
Route::middleware('auth:api')->post('/avancastatus/{id}', function (IndicacaoController $indicacoes, Request $request) {
    return $indicacoes->update($request);
});

Route::get('indicacoes2', [IndicacaoController::class, 'index']);

Route::middleware('auth:api')->get('/indicacoes3', [IndicacaoController::class, 'index']);


// Route::middleware('auth:api')->get('/indicacoes', [IndicacaoController::class, 'index']);

// Listar indicacoes
// 

// Listar uma indicacao
// Route::get('indicacoes/{id}', [IndicacaoController::class, 'show']);

// Criar nova indicacao
// Route::post('indicacao', [IndicacaoController::class, 'store']);

// Deletar uma indicacao
// Route::delete('indicacao/{id}', [IndicacaoController::class,'destroy']);
