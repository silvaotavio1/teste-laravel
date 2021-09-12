<?php

namespace App\Http\Controllers;

use App\Indicacao as Indicacao;
use App\IndicacoesStatus as IndicacoesStatus;
use App\Http\Resources\Indicacao as IndicacaoResource;
use Illuminate\Http\Request;


class IndicacaoController extends Controller
{

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
    $indicacao->email_indicado = $request->input('email_indicado');
    $indicacao->cpf_indicado = $request->input('cpf_indicado');
    $indicacao->cpf_indica = $request->input('cpf_indica');
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

    $status_from = $indicacao['STATUS_ID'];

    $status_id = $status_from < 3 ? ($status_from + 1) : $status_from;

    if($status_from <> $status_id)
    {
      $IndicacoesStatus = new IndicacoesStatus;
      $IndicacoesStatus->status_from = $status_from;
      $IndicacoesStatus->status_id = $status_id;
      $IndicacoesStatus->id = $request->id;
      $IndicacoesStatus->save();
    }
    else
    {
      
    }

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
