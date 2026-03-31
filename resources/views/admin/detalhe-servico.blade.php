@extends('adminlte::page')

@section('content_header')
    <h1>
        {{$servico->os}} <small>{{$servico->nome}}</small>
    </h1>
@stop



@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Erro!</h4>
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> Atenção!</h4>
            {{ session('warning') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-info"></i> Informação</h4>
            {{ session('info') }}
        </div>
    @endif


<script>
    // Colocando o script no início do content para garantir execução imediata se o JS section falhar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script de Webhook carregado.');
        
        const serviceId = '{{$servico->id}}';
        const userNotify = @json(Auth::user()->name);

        const callWebhook = (url, actionName, loadingText) => {
            Swal.fire({
                title: 'Processando...',
                text: loadingText || 'Coletando dados e enviando para o n8n',
                allowOutsideClick: false,
                onBeforeOpen: () => { Swal.showLoading(); }
            });

            fetch(`/api/servico/${serviceId}/data`)
                .then(res => res.json())
                .then(data => {
                    return fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: actionName,
                            service: data,
                            timestamp: new Date().toISOString(),
                            triggered_by: userNotify
                        })
                    });
                })
                .then(res => {
                    return res.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            return { message: text };
                        }
                    });
                })
                .then(result => {
                    console.log(`Resultado do ${actionName}:`, result);
                    
                    let contentHtml = '';
                    let parsedData = null;
                    let rawMessage = '';

                    if (Array.isArray(result) && result.length > 0) {
                        rawMessage = result[0].output || result[0].message || JSON.stringify(result[0]);
                    } else if (result) {
                        rawMessage = result.output || result.message || JSON.stringify(result);
                    }

                    const extractJson = (text) => {
                        if (typeof text !== 'string') return null;
                        const match = text.match(/```json\n([\s\S]*?)\n```/) || text.match(/\{[\s\S]*\}/);
                        if (match) {
                            try {
                                const clean = match[1] ? match[1] : match[0];
                                const parsed = JSON.parse(clean);
                                if (parsed.message || parsed.output) return extractJson(parsed.message || parsed.output) || parsed;
                                return parsed;
                            } catch (e) { return null; }
                        }
                        return null;
                    };

                    parsedData = extractJson(rawMessage);

                    if (parsedData && (parsedData.header || parsedData.scorecard)) {
                         const head = parsedData.header || {};
                         const score = parsedData.scorecard || [];
                         const crono = parsedData.cronologia || [];
                         const diag = parsedData.diagnostico || {};

                         contentHtml = `
                             <div style="text-align: left; font-family: 'Source Sans Pro', sans-serif;">
                                 <!-- 1. Data Header -->
                                 <div style="background: #222d32; color: #fff; padding: 12px; border-radius: 4px; border-left: 5px solid #00c0ef; margin-bottom: 15px;">
                                     <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px; font-size: 11px;">
                                         <div><strong>ID/OS:</strong> ${head.id_os || '---'}</div>
                                         <div><strong>UNID/UF:</strong> ${head.unidade_uf || '---'}</div>
                                         <div style="grid-column: span 2;"><strong>PRAZO TOTAL:</strong> <span class="label label-warning">${head.lead_time || '---'}</span></div>
                                     </div>
                                 </div>

                                 <!-- 2. Matriz de Performance -->
                                 <h5 style="font-weight: bold; color: #333;"><i class="fa fa-dashboard"></i> INDICADORES DE PERFORMANCE</h5>
                                 <table class="table table-bordered table-condensed" style="font-size: 13px; background: #fff; margin-bottom: 20px;">
                                     <thead style="background: #f4f4f4;">
                                         <tr>
                                             <th>Item</th>
                                             <th>Resultado</th>
                                             <th>Referência</th>
                                             <th>Status</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         ${score.map(s => `
                                             <tr>
                                                 <td><strong>${s.kpi}</strong></td>
                                                 <td>${s.resultado}</td>
                                                 <td>${s.benchmark || '---'}</td>
                                                 <td><span class="label label-${(s.status||'').toLowerCase().includes('atrasado') || (s.status||'').toLowerCase().includes('atraso') || (s.status||'').toLowerCase().includes('complexo') || (s.status||'').toLowerCase().includes('critico') ? 'danger' : 'success'}">${s.status}</span></td>
                                             </tr>
                                         `).join('')}
                                     </tbody>
                                 </table>

                                 <!-- 3. Cronologia de Etapas -->
                                 <h5 style="font-weight: bold; color: #333;"><i class="fa fa-clock-o"></i> HISTÓRICO DE ETAPAS</h5>
                                 <div style="display: flex; justify-content: space-between; background: #f9f9f9; padding: 10px; border-radius: 4px; margin-bottom: 20px; border: 1px dashed #ddd;">
                                     ${crono.map((c, i) => `
                                         <div style="text-align: center; flex: 1; position: relative;">
                                             <div style="font-size: 11px; font-weight: bold; color: #777; text-transform: uppercase;">${c.etapa}</div>
                                             <div style="font-size: 16px; font-weight: bold; color: #3c8dbc; margin-top: 5px;">${c.dias}</div>
                                             ${i < crono.length - 1 ? '<div style="position: absolute; right: -5px; top: 20px; color: #ccc;"><i class="fa fa-chevron-right"></i></div>' : ''}
                                         </div>
                                     `).join('')}
                                 </div>

                                 <!-- 4. Diagnóstico de Inteligência -->
                                 <div style="margin-top: 20px; border-top: 2px solid #00c0ef; background: #fff; padding: 15px; border-radius: 0 0 4px 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                     <h5 style="margin-top: 0; margin-bottom: 15px; font-weight: bold; color: #005a71; display: flex; align-items: center; gap: 8px;">
                                         <i class="fa fa-lightbulb-o" style="font-size: 18px;"></i> ANÁLISE E INSIGHTS
                                     </h5>
                                     
                                     <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                         <div style="background: #fdfdfd; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                                             <div style="font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; letter-spacing: 0.5px;">Gargalo Identificado</div>
                                             <div style="margin-top: 5px;"><span class="label label-danger" style="font-size: 11px; padding: 3px 8px;">${diag.gargalo || 'Analítico'}</span></div>
                                         </div>
                                         <div style="background: #fdfdfd; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                                             <div style="font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; letter-spacing: 0.5px;">Vida Útil Restante</div>
                                             <div style="margin-top: 5px; font-size: 14px; font-weight: bold; color: #333;">${diag.vida_util || '---'}</div>
                                         </div>
                                     </div>

                                     <div style="margin-bottom: 15px;">
                                         <div style="font-size: 11px; font-weight: bold; color: #555; margin-bottom: 4px; text-transform: uppercase;"><i class="fa fa-search"></i> Causa Raiz:</div>
                                         <div style="font-size: 13px; color: #444; line-height: 1.5; padding-left: 12px; border-left: 3px solid #ddd; background: #fafafa; padding-top: 5px; padding-bottom: 5px; border-radius: 0 4px 4px 0;">
                                            ${diag.causa_raiz || '---'}
                                         </div>
                                     </div>

                                     <div style="margin-bottom: 15px;">
                                         <div style="font-size: 11px; font-weight: bold; color: #555; margin-bottom: 4px; text-transform: uppercase;"><i class="fa fa-exclamation-triangle"></i> Impacto Operacional:</div>
                                         <div style="font-size: 13px; color: #444; line-height: 1.5; padding-left: 12px; border-left: 3px solid #f39c12; background: #fffcf5; padding-top: 5px; padding-bottom: 5px; border-radius: 0 4px 4px 0;">
                                            ${diag.impacto || '---'}
                                         </div>
                                     </div>

                                     <div style="background: #e7f3f5; padding: 15px; border-radius: 6px; border-left: 5px solid #00c0ef; box-shadow: inset 0 0 10px rgba(0,0,0,0.02);">
                                         <div style="font-size: 11px; font-weight: bold; color: #005a71; margin-bottom: 8px; display: flex; align-items: center; gap: 5px; text-transform: uppercase;">
                                             <i class="fa fa-info-circle"></i> Insight Estratégico (Data BI)
                                         </div>
                                         <div style="font-size: 14px; color: #003a47; font-style: italic; font-family: 'Source Sans Pro', sans-serif; line-height: 1.6;">
                                             "${diag.insight_bi || '---'}"
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         `;
                    }
                    
                    if (!contentHtml) {
                        contentHtml = `
                            <div style="text-align: left; background: #f4f4f4; padding: 15px; border-radius: 5px; border-left: 5px solid #f39c12; margin-top: 10px;">
                                <p style="margin-bottom: 8px; font-weight: bold; color: #555;">Resposta do Servidor:</p>
                                <pre style="font-size: 11px; background: transparent; border: none; padding: 0; color: #333; white-space: pre-wrap; word-break: break-all; margin: 0;">${typeof rawMessage === 'string' ? rawMessage : JSON.stringify(result, null, 2)}</pre>
                            </div>
                        `;
                    }

                    Swal.fire({
                        title: parsedData && parsedData.header ? 'Auditoria de Performance BI' : 'Ação Concluída!',
                        type: 'success',
                        html: contentHtml,
                        width: parsedData ? '680px' : '600px',
                        confirmButtonColor: '#3c8dbc',
                        confirmButtonText: 'FECHAR'
                    });
                })
                .catch(err => {
                    console.error('Erro no webhook:', err);
                    Swal.fire('Erro!', `Falha ao processar o webhook: ${err.message}`, 'error');
                });
        };

        const btnBI = document.getElementById('webhookServiceBtn');

        if (btnBI) {
            btnBI.addEventListener('click', function() {
                const n8nUrl = 'https://n8n.srv1477025.hstgr.cloud/webhook/ac65cd03-e522-4708-a49b-c5a4c681ef4d';
                callWebhook(n8nUrl, 'service_detail_bi', 'Gerando análise estratégica de BI...');
            });
        }
    });
</script>

<div class="row">

<div class="col-md-12">

<div class="pull-right">
            <button id="webhookServiceBtn" class="btn btn-info">
                <i class="fa fa-gear"></i> Auditoria do Serviço
            </button>
            <a href="{{route('timeline.new', $servico->id)}}" class="btn btn-primary" target="_blank">
                <i class="fa fa-clock-o"></i> TIMELINE
            </a>
        </div>

    </div>
</div>

<div class="row">


    <div class="col-md-12">

        @include('admin.components.widget-detalhes')


@if($servico->servicoPrincipal)

<div class="col-md-12">
@include('admin.components.widget-servicoPrincipal')
</div>


@endif


<div class="col-md-12">

    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><b>Detalhes do serviço {{$servico->nome}} @if($servico->servicoPrincipal) <small class="label pull-right bg-red">S</small>@endif</b></h3>

                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

                <div class="col-sm-6">

                  <p><b>Etapa do Processo:</b>

                  @if($servico->situacao == 'finalizado')
                    <button class="btn btn-success">Concluído</button>
                  @else

                  @if(!$servico->protocolo_anexo)
                    <button class="btn btn-primary">Em elaboração</button>
                  @endif

                  @if($servico->protocolo_anexo)
                    @if($servico->laudo_anexo)
                      <button class="btn btn-success">1° análise</button>
                    @endif
                  @endif

                  @endif

                </p>

                <p><b>Nome do Serviço: </b>{{$servico->nome}}</p>
                  <p><b>Situação: </b>
                  @switch($servico->situacao)

	              			@case('andamento')

								@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))

									<button type="button" class="btn btn-xs btn-success">Andamento</button>
	              					@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
	              					<button type="button" class="btn btn-xs btn-danger">Andamento</button>
								@elseif($servico->tipo == 'nRenovaveis')
									<button type="button" class="btn btn-xs btn-warning">Andamento</button>

	              				@endif




	              				@break

	              			@case('finalizado')

	              				@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))

									<button type="button" class="btn btn-xs btn-success">Finalizado</button>
	              					@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
	              					<button type="button" class="btn btn-xs btn-danger">Finalizado</button>

	              				@elseif($servico->tipo == 'nRenovaveis')
									<button type="button" class="btn btn-xs btn-warning">Finalizado</button>

	              				@endif

	              				@break

	              			@case('arquivado')
								<button type="button" class="btn btn-xs btn-default">Arquivado</button>
	              				@break
							@case('standBy')
								<button type="button" class="btn btn-xs btn-gray">Stand By</button>
	              				@break

                        @case('nRenovado')
								<button type="button" class="btn btn-xs btn-default">Não renovado</button>
	              				@break

                        @case('cancelado')
								<button type="button" class="btn btn-xs btn-danger">Cancelado</button>
	              				@break


	              		@endswitch</p>
                  <p><b>Ordem de serviço: </b>{{$servico->os}}</p>
                  <p><b>Proposta: </b>@if($servico->proposta_id)
					      <a href="{{route('proposta.edit',$servico->proposta_id)}}" class="btn btn-info btn-xs">{{$servico->proposta_id}}</a>@else{{$servico->proposta}}@endif</p>

                  <p><b>Responsável: </b>{{$servico->responsavel->name ?? ''}}</p>

                  @if($servico->coresponsavel)
                  <p><b>Co-Responsável: </b>{{$servico->coresponsavel->name ?? ''}}</p>
                  @endif

                  @if($servico->analista1)
                  <p><b>Analista 1: </b>{{$servico->analista1->name ?? ''}}</p>
                  @endif

                  @if($servico->analista2)
                  <p><b>Analista 2: </b>{{$servico->analista2->name ?? ''}}</p>
                  @endif

                  @if($servico->solicitanteServico)
                      <p><b>Solicitante (novo): </b>
                        {{$servico->solicitanteServico->nome}}

                    @else
                    <p><b>Solicitante: </b>
                    {{$servico->solicitante}}

                    @endif



                  </p>

                  <p><b>Departamento:</b> {{$servico->departamento}}</p>

                  <p><b>Início do processo: </b>{{\Carbon\Carbon::parse($servico->created_at)->format('d/m/Y')}}</p>

                  @if(empty($servico->protocolo_anexo))
                  <p><b>Protocolo:</b> <button type="button" class="btn btn-default" data-toggle="modal"
                    data-target="#anexar-protocolo">
                    Anexar
                </button></p>
                @endif

                  @unless ( empty($servico->protocolo_numero) )

                  <p><b>Emissão Protocolo: </b>{{\Carbon\Carbon::parse($servico->protocolo_emissao)->format('d/m/Y')}}</p>
                  <p><b>Número Protocolo: </b>{{$servico->protocolo_numero}}

                    @unless (empty($servico->protocolo_anexo))
                    <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'protocolo']) }}" class="btn btn-xs btn-warning" target="_blank"><i class="fa fa-file"></i> Ver Protocolo</a>
                    @endunless


                  @endunless
                  <p><b>Observações: </b>{!! $servico->observacoes !!}</p>

                </div>

                <div class="col-sm-6">



                  <p><b>Tipo da Licença: </b>
                    @switch($servico->tipoLicenca)
                        @case('renovavel')
                            Renovável
                            @break
                        @case('n/a')
                            Não aplicada
                            @break
                        @case('definitiva')
                            Definitiva
                            @break


                    @endswitch
                  </p>

                  @unless ( empty($servico->licenca_anexo) )
                  @if($servico->tipoLicenca != 'n/a')
                  <p><b>Emissão da Licença: </b>{{\Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y')}}</p>
                  @endif

                  @if($servico->tipoLicenca == 'renovavel')

                    <p><b>Validade da Licença </b>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</p>

                  @endif

                  <p><b>Emissão Documento: </b> <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'licenca']) }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a></p>
                  @endunless


                 @unless (empty($servico->dataFinal))

                 <p><b>Data Final: </b>{{\Carbon\Carbon::parse($servico->dataFinal)->format('d/m/Y')}}</p>
                     
                 @endunless

                 @unless (empty($servico->dataLimiteCiclo))

                 <p><b>Data Limite Ciclo: </b>{{\Carbon\Carbon::parse($servico->dataLimiteCiclo)->format('d/m/Y')}}</p>
                     
                 @endunless


                  @if(empty($servico->laudo_numero))
                  <p><b>Laudo:</b> <button type="button" class="btn btn-default" data-toggle="modal"
                          data-target="#anexar-laudo">
                          Anexar
                      </button></p>
                  @endif

                  @unless ( empty($servico->laudo_anexo) )
                  <p><b>Emissão do Laudo: </b>{{\Carbon\Carbon::parse($servico->laudo_emissao)->format('d/m/Y')}}</p>
                  <p><b>N. do Laudo </b> {{$servico->laudo_numero }}</p>
                  <p><b>Laudo: </b> <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'laudo']) }}" class="btn btn-xs btn-warning" target="_blank"><i class="fa fa-file"></i> Ver Laudo</a></p>
                  @endunless
                  <p><b>Escopo: </b>{!! $servico->escopo !!}</p>

                  @if($servico->licenciamento)
                  <p><b>Licenciamento: </b>{{$servico->licenciamento}}</p>
                  @endif

                </div>

              <a href="{{route('servicos.edit', $servico->id)}}" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span> Editar</a>

              @if(!$servico->servicoPrincipal)
              <a href="{{route('servicos.create', ['id'=>$servico->unidade_id,'t'=>substr($route, 0,7),'tipoServico'=>'nRenovaveis','servicoPrincipal'=>$servico->id])}}" class="btn btn-warning pull-right"><span class="glyphicon glyphicon-plus"></span> SubServiço</a>
              @endif

              @if(count($servico->subServicos))



              {!! Form::open(['route'=>'faturamento.faturarServicoSub','id'=>'cadastroFaturamento', 'target'=>'_blank']) !!}

                {!! Form::hidden('servicos[]',$servico->id) !!}


                {!! Form::hidden('empresa_id',$servico->unidade->empresa_id) !!}

                <button type="submit" class="btn btn-danger pull-right"><i class="fa fa-barcode"></i> Faturar</button>

                {!! Form::close() !!}

                @elseif($servico->servicoPrincipal)

                {!! Form::open(['route'=>'faturamento.faturarServicoSub','id'=>'cadastroFaturamento', 'target'=>'_blank']) !!}

                  {!! Form::hidden('servicos[]',$servico->id) !!}


                  {!! Form::hidden('empresa_id',$servico->unidade->empresa_id) !!}

                  <button type="submit" class="btn btn-danger pull-right"><i class="fa fa-barcode"></i> Faturar</button>

                  {!! Form::close() !!}

              @else

              {!! Form::open(['route'=>'faturamento.step3','id'=>'cadastroFaturamento', 'target'=>'_blank']) !!}

              {!! Form::hidden('servicos[]',$servico->id) !!}
              {!! Form::hidden('empresa_id',$servico->unidade->empresa_id) !!}

              @if(!$servico->faturamento)
                <button type="submit" class="btn btn-danger pull-right"><i class="fa fa-barcode"></i> Faturar</button>

              @else

              <div class="col-md-12">
                <div class="form-group">

                  <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Esse serviço já foi faturado!</h4>
                    <a class="btn btn-warning" href="{{route('faturamento.show', $servico->faturamento->id)}}" target="_blank"><i class="fa fa-file"></i> <span>Acessar relatório</span> </a>
                  </div>
                </div>
              </div>

              @endif
              {!! Form::close() !!}

              @endif



            </div>
            <!-- /.box-body -->

          </div>
</div>



@if(count($servico->subServicos))

<div class="col-md-12">
    @include('admin.components.widget-subServicos')
</div>

@endif




<div class="col-md-7">
  @include('admin.components.widget-taxas')
</div>


<div class="col-md-5">
  @include('admin.components.widget-pendencias')
</div>

<div class="col-md-12">
  @include('admin.components.widget-ordemServico')
</div>


<div class="row">

<div class="col-sm-6">

@include('admin.components.widget-interacoesChat')
</div>


<div class="col-sm-6">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-history"></i> Histórico</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->

             <ul class="timeline timeline-inverse">


                @foreach($servico->ultimasInteracoes as $historico)
                  <!-- timeline item -->
                  <li>

                    <i class="fa fa-user bg-aqua"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:m')}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name ?? 'Robot'}}</a>
                        @if(str_contains($historico->observacoes, 'Alterou solicitante'))
                          @php
                              $id = preg_replace('/[^0-9]/', '', $historico->observacoes);
                              $solicitante = \App\Models\Solicitante::where('id',$id)->value('nome');
                              echo "Alterou solicitante para ".$solicitante;
                          @endphp
                        @elseif(str_contains($historico->observacoes, 'Alterou responsavel_id'))
                        @php
                            $id = preg_replace('/[^0-9]/', '', $historico->observacoes);
                            $solicitante = \App\User::where('id',$id)->value('name');
                            echo "Alterou responsável para ".$solicitante;
                        @endphp
                        @else
                        {{$historico->observacoes}}
                        @endif
                      </h3>


                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach


                  <li>
                   <i class="fa fa-clock bg-gray"></i>
                   <div class="timeline-item">
                      <a href="{{route('interacoes.lista',$servico->id)}}" class="btn btn-flat">Visualizar todo o histórico</a>

                    </div>
                  </li>
                </ul>

                <div class="box-footer">

                <div class="box-header">

                  {!! Form::open(['route'=>'interacao.store']) !!}
                  <div class="input-group">
                  {!! Form::text('observacoes', null, ['class'=>'form-control','id'=>'observacoes','placeholder'=>'Digite a mensagem']) !!}
                  {!! Form::hidden('servico_id',$servico->id) !!}

                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-flat">Enviar</button>
                      </span>
                </div>
                {!! Form::close() !!}

                </div>

            </div>



  </div>


  </div>

</div>


<div class="modal fade" id="anexar-laudo" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
              <h4 class="modal-title">Anexar laudo {{$servico->os}}</h4>
          </div>

          {!! Form::open(['route'=>'servico.anexarLaudo','id'=>'anexarlaudo','enctype'=>'multipart/form-data']) !!}
          <div class="modal-body">

            <div class="form-group">

              {!! Form::hidden('servico_id', $servico->id) !!}

              {!! Form::label('laudo_numero', 'N. laudo', array('class'=>'control-label')) !!}
              {!! Form::text('laudo_numero', null, ['class'=>'form-control','id'=>'laudo_numero']) !!}


            </div>

            <div class="form-group">

              {!! Form::label('laudo_emissao', 'Emissão laudo', array('class'=>'control-label')) !!}
              {!! Form::text('laudo_emissao', null, ['class'=>'form-control','id'=>'laudo_emissao','data-date-format'=>'dd/mm/yyyy']) !!}

            </div>

            <div class="form-group">

              {!! Form::label('laudo_anexo', 'Anexo laudo.', array('class'=>'control-label')) !!}
              {!! Form::file('laudo_anexo', null, ['class'=>'form-control','id'=>'laudo_anexo']) !!}


            </div>

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Anexar</button>
          </div>
      </div>
        {!! Form::close() !!}
  </div>

</div>


<div class="modal fade" id="anexar-protocolo" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
              <h4 class="modal-title">Anexar Protocolo {{$servico->os}}</h4>
          </div>

          {!! Form::open(['route'=>'servico.anexarProtocolo','id'=>'anexarProtocolo','enctype'=>'multipart/form-data']) !!}
          <div class="modal-body">

            <div class="form-group">

              {!! Form::hidden('servico_id', $servico->id) !!}

              {!! Form::label('protocolo_numero', 'N. Protocolo', array('class'=>'control-label')) !!}
              {!! Form::text('protocolo_numero', null, ['class'=>'form-control','id'=>'protocolo_numero']) !!}


            </div>

            <div class="form-group">

              {!! Form::label('protocolo_emissao', 'Emissão Protocolo', array('class'=>'control-label')) !!}
              {!! Form::text('protocolo_emissao', null, ['class'=>'form-control','id'=>'protocolo_emissao','data-date-format'=>'dd/mm/yyyy']) !!}

            </div>

            <div class="form-group">

              {!! Form::label('protocolo_anexo', 'Anexo Protocolo.', array('class'=>'control-label')) !!}
              {!! Form::file('protocolo_anexo', null, ['class'=>'form-control','id'=>'protocolo_anexo']) !!}


            </div>

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Anexar</button>
          </div>
      </div>
        {!! Form::close() !!}
  </div>

</div>

@endsection
