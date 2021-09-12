<?php

namespace App\Http\Controllers;

use App\Indicacao as Indicacao;
use App\Http\Resources\Indicacao as IndicacaoResource;
use Illuminate\Http\Request;

class IndicacaoController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $indicacoes = Indicacao::whereNull('deleted_at')->paginate(15);
    return IndicacaoResource::collection($indicacoes);
  }

  public function show($id)
  {
    $indicacao = Indicacao::findOrFail($id);
    return new IndicacaoResource($indicacao);
  }

  public function store(Request $request)
  {
    $indicacao = new Indicacao;
    $indicacao->codcliente_indicado = $request->input('codcliente_indicado');
    $indicacao->codcliente_indica = $request->input('codcliente_indica');

    if ($indicacao->save()) {
      return new IndicacaoResource($indicacao);
    }
  }

  public function update(Request $request)
  {
    $indicacao = Indicacao::whereNull('deleted_at')->findOrFail($request->id);

    $indicacao = (array) $indicacao;

    foreach ($indicacao as $key => $value) {
      if (strpos(" " . $key, "attributes")) {
        $indicacao = $value;
        break;
      }
    }

    $status_id = $indicacao['STATUS_ID'] < 3 ? ($indicacao['STATUS_ID'] + 1) : $indicacao['STATUS_ID'];

    $indicacao = Indicacao::whereNull('deleted_at')->where('id', $request->id);

    if ($indicacao->update(['status_id' => $status_id])) {
      $indicacao = Indicacao::where('id', $request->id)->first();
      return new IndicacaoResource($indicacao);
    }
  }

  public function destroy($request)
  {
    $indicacao = Indicacao::whereNull('deleted_at')
      ->findOrFail($request->id);

    $indicacao = Indicacao::whereNull('deleted_at')
      ->where('id', $request->id);

    if ($indicacao->update(['deleted_at' => date('Y-m-d H:i:s')])) {
      $indicacao = Indicacao::where('id', $request->id)->first();
      return new IndicacaoResource($indicacao);
    }
  }
}
