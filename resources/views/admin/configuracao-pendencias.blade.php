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
        <!-- Campo Oculto para enviar o mapeamento de renomeações -->
        <input type="hidden" name="renames_content" id="renames_content">

        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-12">
                <a href="{{ route('dashboard') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Voltar ao Dashboard
                </a>
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
                                            <div style="display: flex; align-items: center;">
                                                <button type="button" class="edit-item-btn" style="color: #f39c12; margin-right: 8px; background: none; border: none; padding: 2px 5px;" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" class="remove-item-btn" title="Remover">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
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
        saveConfigAutomatically();
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
                <div style="display: flex; align-items: center;">
                    <button type="button" class="edit-item-btn" style="color: #f39c12; margin-right: 8px; background: none; border: none; padding: 2px 5px;" title="Editar">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" class="remove-item-btn" title="Remover">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </li>
        `;
        
        list.append(html);
        input.val('');
        
        // Re-vincula eventos para o novo elemento
        initDragAndDrop();
        saveConfigAutomatically();
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
            saveConfigAutomatically();
        });
    });

    // Ação de editar item
    $(document).on('click', '.edit-item-btn', function() {
        const item = $(this).closest('.pendencia-item');
        const textSpan = item.find('.pendencia-item-text');
        const oldVal = textSpan.text().trim();
        
        Swal.fire({
            title: 'Editar nome da pendência',
            input: 'text',
            inputValue: oldVal,
            showCancelButton: true,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'O nome da pendência não pode ser vazio!';
                }
            }
        }).then((result) => {
            if (result.value) {
                const newVal = result.value.trim();
                if (newVal === oldVal) return;
                
                // Pergunta se deseja atualizar no banco em lote
                Swal.fire({
                    title: 'Atualizar histórico?',
                    text: 'Deseja alterar o nome em todas as pendências históricas existentes no banco de dados?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, atualizar tudo',
                    cancelButtonText: 'Não, apenas na lista'
                }).then((confirmResult) => {
                    textSpan.text(newVal);
                    
                    let renames = {};
                    const renamesInput = $('#renames_content');
                    if (renamesInput.val()) {
                        try {
                            renames = JSON.parse(renamesInput.val());
                        } catch (e) {
                            renames = {};
                        }
                    }
                    
                    if (confirmResult.value) {
                        // Exibe um loader
                        Swal.fire({
                            title: 'Atualizando...',
                            text: 'Por favor, aguarde enquanto atualizamos as pendências históricas no banco de dados.',
                            allowOutsideClick: false,
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.post('{{ route("admin.renomear_pendencia_ajax") }}', {
                            _token: '{{ csrf_token() }}',
                            old_name: oldVal,
                            new_name: newVal
                        }).done(function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Atualizado com Sucesso!',
                                    text: response.updated_rows + ' pendências foram atualizadas no banco de dados em ' + response.time_taken.toFixed(2) + ' ms.',
                                    type: 'success'
                                }).then(() => {
                                    saveConfigAutomatically();
                                });
                            } else {
                                Swal.fire('Erro!', 'Falha ao atualizar o banco de dados: ' + (response.error || 'Erro desconhecido'), 'error');
                            }
                        }).fail(function(xhr) {
                            Swal.fire('Erro!', 'Erro na requisição. Verifique a conexão com o servidor.', 'error');
                        });

                        // Registra a renomeação local
                        let found = false;
                        for (let originalKey in renames) {
                            if (renames[originalKey] === oldVal) {
                                renames[originalKey] = newVal;
                                found = true;
                                break;
                            }
                        }
                        if (!found) {
                            renames[oldVal] = newVal;
                        }
                    } else {
                        // Escolheu NÃO (ou fechou): remove qualquer agendamento de update do banco para essa chave se houver
                        if (renames[oldVal]) {
                            delete renames[oldVal];
                        }
                        saveConfigAutomatically();
                    }
                    
                    renamesInput.val(JSON.stringify(renames));
                });
            }
        });
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
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

    // Função para salvar a configuração de forma automática via AJAX
    function saveConfigAutomatically() {
        serializeJson();
        const jsonContent = $('#json_content').val();
        
        $.post('{{ route("admin.salvar_configuracao_pendencias") }}', {
            _token: '{{ csrf_token() }}',
            json_content: jsonContent
        }).done(function(response) {
            if (response.success) {
                Toast.fire({
                    type: 'success',
                    title: 'Alterações salvas!'
                });
            } else {
                Toast.fire({
                    type: 'error',
                    title: 'Erro ao salvar alterações.'
                });
            }
        }).fail(function() {
            Toast.fire({
                type: 'error',
                title: 'Erro de conexão ao salvar.'
            });
        });
    }

    // Inicializa Drag and Drop
    initDragAndDrop();
});
</script>
@stop
