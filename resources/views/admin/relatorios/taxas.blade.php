<div class="box">
  <div class="box-header"></div>
  <div class="box-body">
  <table id="relatorio-completo" class="table table-bordered" >
          <thead>
          <tr>
            <th>Empresa</th>
            <th>Serviço</th>
            <th>OS</th>
            <th>Código</th>
            <th>Unidade</th>
            <th>CNPJ</th>
            <th>Cidade/UF</th>
            <th>Proposta</th>
            <th>Valor Total</th>
            <th>Taxa</th>
            <th>Emissão</th>
            <th>Vencimento</th>
            <th>Pagamento</th>
            <th>Reembolso</th>
            <th>Status</th>
            <th>Valor</th>
            
          </tr>
          </thead>
          <tbody>
            
               @foreach($servicos as $s)

                @foreach($s->taxas as $t)

                  <tr>
                    <td>{{$s->unidade->empresa->nomeFantasia}}</td>
                    <td>{{$s->nome}}</td>
                    <td>{{$s->os}}</td>
                    <td>{{$s->unidade->codigo}}</td>
                    <td>{{$s->unidade->nomeFantasia}}</td>
                    <td>{{$s->unidade->cnpj}}</td>
                    <td>{{$s->unidade->cidade}}/{{$s->unidade->uf}}</td>
                    <td>{{$s->proposta ?? ''}}</td>
                    <td>{{$s->financeiro->valorTotal ?? ''}}</td>
                    <td>{{$t->nome}}</td>
                    <td>{{ \Carbon\Carbon::parse($t->emissao)->format('d/m/Y')}}</td>
                    <td>{{ \Carbon\Carbon::parse($t->vencimento)->format('d/m/Y')}}</td>
                    <td>{{ \Carbon\Carbon::parse($t->pagamento)->format('d/m/Y')}}</td>
                    <td>{{$t->reembolso}}</td>
                    <td>{{$t->situacao}}</td>
                    <td>{{$t->valor}}</td>
                  </tr>

                @endforeach

               @endforeach 


          </tbody>
          
        </table>
  </div>
</div>




