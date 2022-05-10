


<div class="box">
  <div class="box-header"></div>
  <div class="box-body">
  <table id="relatorio-completo" class="table table-bordered" >
          <thead>
          <tr>
            <th>Razão Social</th>
            <th>Código</th>
            <th>Nome</th>
            <th>CNPJ</th>
            <th>Status</th>
            <th>Imóvel</th>
            <th>Ins. Estadual</th>
            <th>Ins. Municipal</th>
            <th>Inscrição Imob.</th>
            <th>Matrícula RI</th>
            <th>Área da Loja</th>
            <th>Endereço</th>
            <th>Número</th>
            <th>Complemento</th>
            <th>Cidade/UF</th>
            <th>CEP</th>
            <th>Tipo</th>
            <th>Ordem de Serviço</th>
            <th>Situação</th>
            <th>Responsável</th>
            <th>Nome</th>
            <th>Solicitante</th>
            <th>N° Protocolo</th>
            <th>Emissao Protocolo</th>
            <th>Tipo Licença</th>
            <th>Proposta</th>
            <th>Emissão da licença</th>
            <th>Validade da licença</th>
            <th>Valor Total</th>
            <th>Valor em Aberto</th>
            <th>Finalizado</th>
            <th>Criação</th>
            
          </tr>
          </thead>
          <tbody>
            
               @foreach($servicos as $s)

                <tr>
                    <td>{{$s->unidade->razaoSocial}}</td>
                    <td>{{$s->unidade->codigo}}</td>
                    <td>{{$s->unidade->nomeFantasia}}</td>
                    <td>{{$s->unidade->cnpj}}</td>
                    <td>{{$s->unidade->status}}</td>
                    <td>{{$s->unidade->tipoImovel}}</td>
                    <td>{{$s->unidade->inscricaoEst}}</td>
                    <td>{{$s->unidade->inscricaoMun}}</td>
                    <td>{{$s->unidade->inscricaoImo}}</td>
                    <td>{{$s->unidade->matriculaRI}}</td>
                    <td>{{$s->unidade->area}}</td>

                    <td>{{$s->unidade->endereco}}</td>
                    <td>{{$s->unidade->numero}}</td>
                    <td>{{$s->unidade->complemento}}</td>
                    <td>{{$s->unidade->cidade}}/{{$s->unidade->uf}}</td>
                    <td>{{$s->unidade->cep}}</td>
                    <td>{{$s->tipo}}</td>

                    
                    <td>{{$s->os}}</td>
                    <td>{{$s->situacao}}</td>
                    <td>{{$s->responsavel->name}}</td>
                    <td>{{$s->nome}}</td>
                    
                    <td>@if(!is_numeric($s->solicitante))
                      {{$s->solicitante}}
                      @else
                      {{\App\Models\Solicitante::where('id',$s->solicitante)->value('nome')}}
                      @endif</td>

                    <td>{{$s->protocolo_numero}}</td>
                    <td>{{ \Carbon\Carbon::parse($s->protocolo_emissao)->format('d/m/Y')}}</td>

                    <td>{{$s->tipoLicenca}}</td>
                    <td>{{$s->proposta}}</td> 
                    <td>{{ \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y')}}</td>
                    <td>{{ \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y')}}</td>
                    
                    <td>{{$s->financeiro->valorTotal ?? '0'}}</td>
                    <td>{{$s->financeiro->valorAberto ?? '0'}}</td>
                    <td>@if(isset($s->servicoFinalizado)){{ \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y') ?? 'N/A'}}@endif</td>
                    <td>{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y') ?? 'N/A'}}</td>

                </tr>

               @endforeach 


          </tbody>
          
        </table>
  </div>
</div>






@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>

	

$(function () {
		    $('#relatorio-completo').DataTable({
		      "paging": false,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": false,
		      "autoWidth": true,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }


		    });
		     $('.confirmation').on('click', function () {
        		return confirm('Você deseja excluir a empresa?\nTodos os dados relacionados a ela serão excluidos.');
    			});
  });



    </script>
  @stop