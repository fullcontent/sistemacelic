@extends('adminlte::page')

@section('content_header')
    <h1>
        Editar Mensagem de Histórico
    </h1>
@stop

@section('content')

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Erro!</h4>
            {{ session('error') }}
        </div>
    @endif

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-pencil"></i> Edição de Mensagem (Serviço O.S. {{$historico->servico->os}})</h3>
        </div>
        
        {!! Form::model($historico, ['route' => ['interacao.update', $historico->id], 'method' => 'POST']) !!}
            <div class="box-body">
                <input type="hidden" name="return_url" value="{{ url()->previous() }}">
                
                <div class="form-group">
                    <label for="observacoes">Mensagem</label>
                    <textarea id="observacoes" name="observacoes" class="form-control" rows="8" required spellcheck="true" lang="pt-BR">{{ strip_tags($historico->observacoes) }}</textarea>
                </div>
                
                <div class="form-group">
                    <label for="pendencia_id"><i class="fa fa-link"></i> Pendência Vinculada</label>
                    <select name="pendencia_id" id="pendencia_id" class="form-control">
                        <option value="">Nenhuma pendência</option>
                        @foreach($pendencias->where('status', '!=', 'concluido') as $p)
                            <option value="{{$p->id}}" {{ $historico->pendencia_id == $p->id ? 'selected' : '' }}>{{$p->pendencia}}</option>
                        @endforeach
                        
                        @if($historico->pendencia_id && $historico->pendencia && $historico->pendencia->status == 'concluido')
                            <option value="{{$historico->pendencia_id}}" selected>{{$historico->pendencia->pendencia}} (Concluída)</option>
                        @endif
                    </select>
                </div>
            </div>
            
            <div class="box-footer">
                <a href="{{ url()->previous() }}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Voltar</a>
                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Salvar alterações</button>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
