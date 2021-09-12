<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: *");

if(isset($_SERVER['HTTP_ORIGIN']))
{
   header("Access-Control-Allow-Origin: *");
   header('Access-Control-Allow-Credentials: true');
   header("Access-Control-Allow-Methods: *");
}
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{

   if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
      header("Access-Control-Allow-Methods: *");
   if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
      header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
   exit(0);
}

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndicacaoController;

/*
|__________________________________________________________________________|
|--------------------------------------------------------------------------|
|--------------------------------------------------------------------------|
| TESTE - LARAVEL - OTAVIO                                                 |
|--------------------------------------------------------------------------|
|--------------------------------------------------------------------------|
|__________________________________________________________________________|
*/

//Resposta para uma rota inexistente
Route::fallback(function () {
    return response()->json(['error' => 'Rota inexistente!'], 404);
});

//Teste de middleware - Rota base
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
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