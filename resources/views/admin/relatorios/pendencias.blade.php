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
            <th>Data Inauguração</th>
            <th>CNPJ</th>
            <th>Cidade/UF</th>
           
            <th>Pendencia</th>
            <th>Responsabilidade</th>
            <th>Responsável</th>
            <th>Status</th>
            <th>Vencimento</th>
            
          </tr>
          </thead>
          <tbody>
            
               @foreach($servicos as $s)

                @foreach($s->pendencias as $p)

                  <tr>
                    <td>{{$s->unidade->empresa->nomeFantasia}}</td>
                    <td>{{$s->nome}}</td>
                    <td>{{$s->os}}</td>
                    <td>{{$s->unidade->codigo}}</td>
                    <td>{{$s->unidade->nomeFantasia}}</td>
                    <td>{{\Carbon\Carbon::parse($s->unidade->dataInauguracao)->format('d/m/Y') ?? ''}}</td>
                    <td>{{$s->unidade->cnpj}}</td>
                    <td>{{$s->unidade->cidade}}/{{$s->unidade->uf}}</td>
                    
                    <td>{{$p->pendencia}}</td>
                    <td>{{$p->responsavel_tipo}}</td>
                    <td>{{$p->responsavel->name ?? ''}}</td>
                    
                    <td>{{$p->status}}</td>
                    <td>{{$p->vencimento}}</td>
                  </tr>

                @endforeach

               @endforeach 


          </tbody>
          
        </table>
  </div>
</div>




