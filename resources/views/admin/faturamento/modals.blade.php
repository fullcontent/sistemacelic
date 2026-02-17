<!-- Modal Editar NF -->
<div class="modal fade" id="editNF">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Editar NF</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'faturamento.editNF']) !!}
                {!! Form::hidden('faturamentoID', null, ['class' => 'form-control', 'id' => 'faturamentoID']) !!}

                <div class="form-group">
                    {!! Form::label('faturamentoNome', 'Faturamento', array('class' => 'control-label')) !!}
                    {!! Form::text('faturamentoNome', null, ['class' => 'form-control', 'disabled' => true, 'id' => 'faturamentoNome']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('faturamentoCliente', 'Cliente', array('class' => 'control-label')) !!}
                    {!! Form::text('faturamentoCliente', null, ['class' => 'form-control', 'disabled' => true, 'id' => 'faturamentoCliente']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('nf', 'N.F.', array('class' => 'control-label')) !!}
                    {!! Form::text('nf', null, ['class' => 'form-control', 'id' => 'faturamentoNF', 'required' => true]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-info">Editar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Modal Selecionar Empresa (CNPJ) -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Alterar CNPJ Contratado</h4>
            </div>
            <div class="modal-body">
                <form id="company-select-form">
                    @csrf
                    <div class="form-group">
                        <label>Selecione a Empresa:</label>
                        <select name="dadosCastro_id" class="form-control"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary save-selected-item">Salvar Alteração</button>
            </div>
        </div>
    </div>
</div>