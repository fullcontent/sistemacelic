<div class="box box-default">
    <div class="box-header with-border">
        <a href="#" data-widget="collapse">
            <h3 class="box-title"><i class="fa fa-inbox text-gray"></i> Serviços Arquivados</h3>
        </a>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="servicosArquivadosTable">
                <thead>
                    <tr>
                        <th>OS</th>
                        <th>Serviço</th>
                        <th>Tipo</th>
                        <th>Unidade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servicos->where('situacao', 'arquivado') as $servico)
                    <tr>
                        <td><code>{{ $servico->os }}</code></td>
                        <td>
                            <a href="{{ route('cliente.servico.show', $servico->id) }}">
                                <strong>{{ $servico->nome }}</strong>
                            </a>
                        </td>
                        <td>
                            @switch($servico->tipo)
                                @case('licencaOperacao')
                                    Licença de Operação
                                    @break
                                @case('nRenovaveis')
                                    Não Renovável
                                    @break
                                @case('controleCertidoes')
                                    Certidão
                                    @break
                                @case('controleTaxas')
                                    Taxa
                                    @break
                                @case('facilitiesRealEstate')
                                    Facilities/Real Estate
                                    @break
                                @default
                                    Outro
                            @endswitch
                        </td>
                        <td>
                            @if($servico->unidade)
                                <a href="{{ route('cliente.unidade.show', $servico->unidade->id) }}">
                                    {{ $servico->unidade->nomeFantasia }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="label bg-gray">Arquivado</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.box-body -->
</div>
