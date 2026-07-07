@extends('adminlte::page')

@section('title', 'Configurar Pendências')

@section('content_header')
    <h1>Configuração da Lista de Pendências</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-ban"></i> Erro!</h4>
                {!! $error !!}
            </div>
        @endforeach
    @endif

    @php
        $decoded = json_decode($jsonContent, true) ?: [];
        $groups = [
            'Responsabilidade Castro' => 'usuario',
            'Responsabilidade Cliente' => 'cliente',
            'Responsabilidade Órgão' => 'op',
            'Vinculada' => 'vinculada'
        ];
    @endphp

    <style>
        .pendencia-item {
            cursor: grab;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            border-radius: 4px;
            background-color: #fafafa;
            border: 1px solid #e3e3e3;
            padding: 10px 15px;
            transition: all 0.2s ease;
        }
        .pendencia-item:hover {
            background-color: #f4f4f4;
            border-color: #d2d2d2;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .pendencia-item.dragging {
            opacity: 0.4;
            border: 1px dashed #3c8dbc;
            background-color: #ecf0f5;
        }
        .pendencia-item.over {
            border-top: 2px solid #3c8dbc;
            background-color: #ecf0f5;
        }
        .remove-item-btn {
            background: none;
            border: none;
            color: #dd4b39;
            cursor: pointer;
            padding: 2px 5px;
            border-radius: 3px;
            transition: background 0.15s;
        }
        .remove-item-btn:hover {
            background-color: rgba(221, 75, 57, 0.1);
        }
        .add-group-form {
            margin-top: 15px;
            border-top: 1px solid #f4f4f4;
            padding-top: 15px;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" style="background-color: #00c0ef !important; border-color: #00acd6; color: #fff !important; margin-bottom: 20px;">
                <h5><i class="icon fa fa-info"></i> Painel de Gerenciamento de Pendências</h5>
                <p>Aqui você pode adicionar, remover e ordenar as opções de pendências exibidas no "dropbox" (dropdown). Arraste os itens pelas barras <i class="fa fa-bars"></i> para reordenar a lista conforme sua preferência.</p>
                <p><strong>Atenção:</strong> Alterar ou deletar opções neste menu não alterará o nome das pendências já criadas nos serviços anteriores, evitando conflitos no banco de dados.</p>
            </div>
        </div>
    </div>

    {!! Form::open(['route' => 'admin.salvar_configuracao_pendencias', 'method' => 'POST', 'id' => 'form-pendencias-config']) !!}
        <!-- Campo Oculto para enviar o JSON serializado -->
        <input type="hidden" name="json_content" id="json_content">

        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-12">
                <a href="{{ route('dashboard') }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-primary pull-right">
                    <i class="fa fa-save"></i> Salvar Configuração
                </button>
            </div>
        </div>

        <div class="row">
            @foreach($groups as $label => $key)
                <div class="col-md-3">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $label }}</h3>
                        </div>
                        <div class="box-body" style="max-height: 400px; overflow-y: auto;">
                            <ul class="list-group list-group-sortable" id="group-{{ $key }}" data-group-name="{{ $label }}" style="min-height: 50px; padding-bottom: 20px;">
                                @if(isset($decoded[$label]) && is_array($decoded[$label]))
                                    @foreach($decoded[$label] as $item)
                                        <li class="list-group-item pendencia-item" draggable="true">
                                            <div>
                                                <i class="fa fa-bars text-muted" style="margin-right: 10px; cursor: grab;"></i>
                                                <span class="pendencia-item-text">{{ $item }}</span>
                                            </div>
                                            <button type="button" class="remove-item-btn" title="Remover">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="box-footer">
                            <div class="input-group">
                                <input type="text" class="form-control new-item-input" placeholder="Nova pendência para este grupo...">
                                <span class="input-group-btn">
                                    <button class="btn btn-success add-item-btn" type="button">
                                        <i class="fa fa-plus"></i> Adicionar
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    {!! Form::close() !!}
@stop

@section('js')
<script>
$(document).ready(function() {
    let dragSource = null;

    // Vincula drag and drop nas li existentes
    function initDragAndDrop() {
        const items = document.querySelectorAll('.pendencia-item');
        items.forEach(item => {
            item.removeEventListener('dragstart', handleDragStart);
            item.removeEventListener('dragenter', handleDragEnter);
            item.removeEventListener('dragover', handleDragOver);
            item.removeEventListener('dragleave', handleDragLeave);
            item.removeEventListener('drop', handleDrop);
            item.removeEventListener('dragend', handleDragEnd);

            item.addEventListener('dragstart', handleDragStart, false);
            item.addEventListener('dragenter', handleDragEnter, false);
            item.addEventListener('dragover', handleDragOver, false);
            item.addEventListener('dragleave', handleDragLeave, false);
            item.addEventListener('drop', handleDrop, false);
            item.addEventListener('dragend', handleDragEnd, false);
        });
    }

    function handleDragStart(e) {
        dragSource = this;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
        this.classList.add('dragging');
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDragEnter(e) {
        this.classList.add('over');
    }

    function handleDragLeave(e) {
        this.classList.remove('over');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        
        // Garante que só move dentro do mesmo grupo
        if (dragSource !== this && dragSource.parentNode === this.parentNode) {
            const list = this.parentNode;
            const nodes = Array.from(list.children);
            const dragIdx = nodes.indexOf(dragSource);
            const targetIdx = nodes.indexOf(this);
            
            if (dragIdx < targetIdx) {
                list.insertBefore(dragSource, this.nextSibling);
            } else {
                list.insertBefore(dragSource, this);
            }
        }
        return false;
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        document.querySelectorAll('.pendencia-item').forEach(item => {
            item.classList.remove('over');
        });
        serializeJson();
    }

    // Ação de adicionar item
    $('.add-item-btn').click(function() {
        const box = $(this).closest('.box');
        const input = box.find('.new-item-input');
        const val = input.val().trim();
        
        if (val === '') {
            return;
        }

        const list = box.find('.list-group-sortable');
        
        // Cria elemento HTML
        const html = `
            <li class="list-group-item pendencia-item" draggable="true">
                <div>
                    <i class="fa fa-bars text-muted" style="margin-right: 10px; cursor: grab;"></i>
                    <span class="pendencia-item-text">${val}</span>
                </div>
                <button type="button" class="remove-item-btn" title="Remover">
                    <i class="fa fa-trash"></i>
                </button>
            </li>
        `;
        
        list.append(html);
        input.val('');
        
        // Re-vincula eventos para o novo elemento
        initDragAndDrop();
        serializeJson();
    });

    // Permitir adicionar apertando Enter
    $('.new-item-input').keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $(this).closest('.box').find('.add-item-btn').click();
        }
    });

    // Ação de remover item (usa delegação de eventos para funcionar com novos itens)
    $(document).on('click', '.remove-item-btn', function() {
        const item = $(this).closest('.pendencia-item');
        item.fadeOut(200, function() {
            item.remove();
            serializeJson();
        });
    });

    // Função de serialização em JSON
    function serializeJson() {
        const data = {};
        $('.list-group-sortable').each(function() {
            const groupName = $(this).data('group-name');
            data[groupName] = [];
            $(this).find('.pendencia-item-text').each(function() {
                const text = $(this).text().trim();
                if (text) {
                    data[groupName].push(text);
                }
            });
        });
        $('#json_content').val(JSON.stringify(data, null, 4));
    }

    // Inicializa Drag and Drop e monta o JSON antes do envio
    initDragAndDrop();
    
    $('#form-pendencias-config').submit(function(e) {
        serializeJson();
    });
});
</script>
@stop
