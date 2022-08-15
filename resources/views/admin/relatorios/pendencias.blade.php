


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
           
            <th>Pendencia</th>
            <th>Status</th>
            <th>Vencimento</th>
            
          </tr>
          </thead>
          <tbody>
            
               @foreach($servicos as $s)

                @foreach($s->pendencias as $p)

                  <tr>
                    <td>{{$s->nome}}</td>
                    <td>{{$s->os}}</td>
                    <td>{{$s->unidade->codigo}}</td>
                    <td>{{$s->unidade->nomeFantasia}}</td>
                    <td>{{$s->unidade->cnpj}}</td>
                    <td>{{$s->unidade->cidade}}/{{$s->unidade->uf}}</td>
                    
                    <td>{{$p->pendencia}}</td>
                    
                    <td>{{$p->status}}</td>
                    <td>{{$p->vencimento}}</td>
                  </tr>

                @endforeach

               @endforeach 


          </tbody>
          
        </table>
  </div>
</div>




