<?php

namespace App\Http\Controllers;

use App\Indicacao as Indicacao;
use App\IndicacoesStatus as IndicacoesStatus;
use App\Http\Resources\Indicacao as IndicacaoResource;
use App\Http\Resources\ValidaCPFCNPJ;
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

    $email_indicado = $request->input('email_indicado');
    $cpf_indicado = $request->input('cpf_indicado');
    $cpf_indica = $request->input('cpf_indica');


    
    #--------------------------------------Validação do email----------------------------------------------
    if (!filter_var($email_indicado, FILTER_VALIDATE_EMAIL)) {
      return response()->json(['error' => 'Email inválido'], 400);
    }
    #-------------------------------------------FIM-------------------------------------------------------



    #------------------------Validação de CPF do indicado e do divulgador----------------------------------
    $valida_cpf_indicado = new ValidaCPFCNPJ($cpf_indicado);
    $valida_cpf_indica = new ValidaCPFCNPJ($cpf_indica);

    if (!$valida_cpf_indica->valida()) {
      return response()->json(['error' => 'CPF ou CNPJ Inválido [indica]'], 400);
    } elseif (!$valida_cpf_indicado->valida()) {
      return response()->json(['error' => 'CPF ou CNPJ Inválido [indicado]'], 400);
    }
    #-------------------------------------------FIM-------------------------------------------------------



    #---------------------Verifica se o cpf_indicado é igual ao cpf_indica---------------------------------
    if ($cpf_indicado == $cpf_indica) {
      return response()->json(['error' => 'O CPF indica deve ser diferente do CPF indicado'], 403);
    }
    #-------------------------------------------FIM-------------------------------------------------------



    #---------------------Verificação se o cpf já não foi indicado anteriormente---------------------------
    $count_indicado = Indicacao::where('cpf_indicado', $cpf_indicado)->get()->count();
    if ($count_indicado > 0) {
      return response()->json(['error' => 'O CPF iformado já foi indicado!'], 403);
    }
    #-------------------------------------------FIM-------------------------------------------------------



    #------------------------Insere nova indicação após às etapas de validação-----------------------------
    $indicacao->email_indicado = $email_indicado;
    $indicacao->cpf_indicado = $cpf_indicado;
    $indicacao->cpf_indica = $cpf_indica;

    if ($indicacao->save()) {
      return new IndicacaoResource($indicacao);
    }
    #-------------------------------------------FIM-------------------------------------------------------
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

    if ($status_from <> $status_id) {
      $IndicacoesStatus = new IndicacoesStatus;
      $IndicacoesStatus->status_id_from = $status_from;
      $IndicacoesStatus->status_id = $status_id;
      $IndicacoesStatus->user_alt = $request->user()['description'];
      $IndicacoesStatus->id = $request->id;
      $IndicacoesStatus->save();
    } else {
      return response()->json(['error' => 'Indicação já esta no status FINALIZADA!'], 403);
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
