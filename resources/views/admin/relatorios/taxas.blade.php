


<div class="box">
  <div class="box-header"></div>
  <div class="box-body">
  <table id="relatorio-completo" class="table table-bordered" >
          <thead>
          <tr>
            <th>Serviço</th>
            <th>OS</th>
            <th>Código</th>
            <th>Unidade</th>
            <th>CNPJ</th>
            <th>Cidade/UF</th>
            <th>Proposta</th>
            <th>Valor Total</th>
            <th>Taxa</th>
            <th>Status</th>
            <th>Valor</th>
            
          </tr>
          </thead>
          <tbody>
            
               @foreach($servicos as $s)

                @foreach($s->taxas as $t)

                  <tr>
                    <td>{{$s->nome}}</td>
                    <td>{{$s->os}}</td>
                    <td>{{$s->unidade->codigo}}</td>
                    <td>{{$s->unidade->nomeFantasia}}</td>
                    <td>{{$s->unidade->cnpj}}</td>
                    <td>{{$s->unidade->cidade}}/{{$s->unidade->uf}}</td>
                    <td>{{$s->proposta ?? ''}}</td>
                    <td>{{$s->financeiro->valorTotal ?? ''}}</td>
                    <td>{{$t->nome}}</td>
                    <td>{{$t->situacao}}</td>
                    <td>{{$t->valor}}</td>
                  </tr>

                @endforeach

               @endforeach 


          </tbody>
          
        </table>
  </div>
</div>




