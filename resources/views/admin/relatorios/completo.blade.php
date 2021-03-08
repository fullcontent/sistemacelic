<table class="table table-bordered">
          <thead>
          <tr>
            <th>Razão Social</th>
            <th>Código</th>
            <th>Nome</th>
            <th>CNPJ</th>
            <th>Status</th>
            <th>Imóvel</th>
            <th>Endereço</th>
            <th>Número</th>
            <th>Complemento</th>
            <th>Cidade/UF</th>
            <th>CEP</th>
            <th>Ordem de Serviço</th>
            <th>Nome</th>
            <th>Responsável</th>
            <th>Solicitante</th>
            <th>Emissão da licença</th>
            <th>Validade da licença</th>
            
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
                    <td>{{$s->unidade->endereco}}</td>
                    <td>{{$s->unidade->numero}}</td>
                    <td>{{$s->unidade->complemento}}</td>
                    <td>{{$s->unidade->cidade}}/{{$s->unidade->uf}}</td>
                    <td>{{$s->unidade->cep}}</td>
                    <td>{{$s->os}}</td>
                    <td>{{$s->nome}}</td>
                    <td>{{$s->responsavel->name}}</td>
                    <td>{{$s->solicitante}}</td>
                    <td>{{ \Carbon\Carbon::parse($s->licenca_emissao)->format('d/m/Y')}}</td>
                    <td>{{ \Carbon\Carbon::parse($s->licenca_validade)->format('d/m/Y')}}</td>
                    
                </tr>

               @endforeach 


          </tbody>
          
        </table>