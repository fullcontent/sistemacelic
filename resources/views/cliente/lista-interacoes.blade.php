@extends('adminlte::page')

@section('content_header')
    <h1>
        Interações da O.S. {{$servico->os}} <small>{{$servico->nome}}</small>
    </h1>
@stop

@section('content')

    <div style="margin-bottom: 15px;">
        <a href="{{route('cliente.servico.show', $servico->id)}}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Voltar para o Serviço</a>
    </div>

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-history"></i> Histórico de Interações</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            @if(count($interacoes) > 0)
                <ul class="timeline timeline-inverse">
                    @foreach($interacoes as $historico)
                        @php
                            if($historico->user->privileges == 'cliente') {
                                $label = "fa fa-user bg-red";
                            } elseif($historico->user->privileges == 'admin') {
                                $label = "fa fa-copyright bg-aqua";
                            } else {
                                $label = "fa fa-weixin bg-blue";
                            }
                        @endphp
                        <!-- timeline item -->
                        <li>
                            <i class="{{$label}}"></i>
                            
                            <div class="timeline-item" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; background: #fff; margin-bottom: 15px;">
                                <span class="time" style="padding: 10px 15px;">
                                    <i class="fa fa-clock-o"></i> 
                                    {{\Carbon\Carbon::parse($historico->edited_at ?? $historico->created_at)->timezone('America/Sao_Paulo')->format('d/m/Y H:i')}}
                                    @if($historico->edited_at)
                                        <span class="text-warning" style="font-size: 10px; font-weight: bold; margin-left: 2px;">(editado)</span>
                                    @endif
                                </span>
                                
                                <h3 class="timeline-header" style="border-bottom: none; padding: 10px 15px; font-size: 14px; font-weight: bold;">
                                    <strong>{{$historico->user->name ?? 'Robot'}}</strong>
                                    <small class="text-muted" style="display: block; font-size: 11px; margin-top: 2px; font-weight: normal;">
                                        {{$historico->user->privileges == 'admin' ? 'Admin' : 'Operacional'}}
                                    </small>
                                </h3>

                                <div class="timeline-body" style="padding: 5px 15px 10px 15px; font-size: 13px; color: #444; border-bottom: none;">
                                    {{$historico->observacoes}}
                                </div>

                                <div class="timeline-footer" style="padding: 5px 15px 10px 15px; background: transparent; border-top: none; display: flex; align-items: center; gap: 8px;">
                                    @if($historico->pendencia)
                                        <div class="pendencia-badge" style="display: inline-flex; border: 1px solid #3c8dbc; border-radius: 4px; overflow: hidden; font-size: 11px; vertical-align: middle;">
                                            <span style="background: #3c8dbc; color: #fff; padding: 3px 8px; font-weight: normal;"><i class="fa fa-link"></i> Vinculado à Pendência:</span>
                                            <span class="pendencia-nome-span" style="background: #f4f4f4; color: #444; padding: 3px 8px; font-weight: bold;">{{$historico->pendencia->pendencia}}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-muted" style="padding: 20px;">
                    <i class="fa fa-info-circle" style="font-size: 24px; margin-bottom: 10px;"></i>
                    <p>Nenhuma interação registrada para esta O.S.</p>
                </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>

@endsection