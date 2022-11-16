<div class="box-body">
  

    @if($errors->any())
    {!! implode('', $errors->all('<div class="callout callout-danger">:message</div>')) !!}
@endif

      

      <div class="col-md-12">
        
        <div class="form-group">
          
          {!! Form::label('empresa_id', 'Empresa', array('class'=>'control-label')) !!}
          
          {!! Form::select('empresa_id', $empresas, null, ['class'=>'form-control']) !!}

        </div>

      </div>

      <div class="col-md-6">
        
        <div class="input-group input-group-sm">
                  
                  {!! Form::label('cnpj', 'CNPJ', array('class'=>'control-label')) !!}
                  {!! Form::text('cnpj', null, ['class'=>'form-control','id'=>'cnpj']) !!}
                  <span class="input-group-btn" style="vertical-align: bottom;">
         <button type="button" class="btn btn-warning btn-flat" id="pesquisar">Pesquisar</button>
        </span>
              </div>

      </div>


      
      <div class="col-md-6">
        <div class="form-group">
                  
                  {!! Form::label('nomeFantasia', 'Nome Fantasia', array('class'=>'control-label')) !!}
                  {!! Form::text('nomeFantasia', null, ['class'=>'form-control','id'=>'nomeFantasia']) !!}
        
              </div>

      </div>
            
        <div class="col-md-8">
              
        <div class="form-group">
                  
                  {!! Form::label('razaoSocial', 'Razão Social', array('class'=>'control-label')) !!}
                  {!! Form::text('razaoSocial', null, ['class'=>'form-control','id'=>'razaoSocial']) !!}
        
              </div>

        </div>

        <div class="col-md-2">
          
          <div class="form-group">
                  
                  {!! Form::label('status', 'Status', array('class'=>'control-label')) !!}
                  {!! Form::select('status', array('ativa' => 'Ativa', 'inativa' => 'Inativa','prospeccao'=>'Prospecção','inauguracao'=>'Inauguração'), null, ['class'=>'form-control'])!!}
        
              </div>

        </div>

        <div class="col-md-2">
          
            <div class="form-group">
                    
                    {!! Form::label('dataInauguracao', 'Data Inauguração', array('class'=>'control-label')) !!}
                    @if(Route::is('unidade.show'))
                    {!! Form::text('dataInauguracao', \Carbon\Carbon::parse($unidades->dataInauguracao)->format('d/m/Y'), ['class'=>'form-control','id'=>'dataInauguracao','data-date-format'=>'dd/mm/yyyy']) !!}
                    @else
                    {!! Form::text('dataInauguracao', null, ['class'=>'form-control','id'=>'dataInauguracao','data-date-format'=>'dd/mm/yyyy']) !!}
                    @endif 
          
                </div>
  
          </div>

            
            <div class="col-md-2">
              
              <div class="form-group">
                  
                  {!! Form::label('codigo', 'Código', array('class'=>'control-label')) !!}
                  {!! Form::text('codigo', null, ['class'=>'form-control','id'=>'codigo']) !!}
        
              </div>

            </div>

            <div class="col-md-3">
              
              <div class="form-group">
                  
                  {!! Form::label('inscricaoEst', 'Inscrição Estadual', array('class'=>'control-label')) !!}
                  {!! Form::text('inscricaoEst', null, ['class'=>'form-control','id'=>'inscricaoEst']) !!}
        
              </div>

            </div>



            
            <div class="col-md-3">
              
              <div class="form-group">
                  
                  {!! Form::label('inscricaoMun', 'Inscrição Municipal', array('class'=>'control-label')) !!}
                  {!! Form::text('inscricaoMun', null, ['class'=>'form-control','id'=>'inscricaoMun']) !!}
        
              </div>
            </div>

            <div class="col-md-2">
                
                <div class="form-group">
                    
                    {!! Form::label('inscricaoImo', 'Inscrição Imobiliária', array('class'=>'control-label')) !!}
                    {!! Form::text('inscricaoImo', null, ['class'=>'form-control','id'=>'inscricaoImo']) !!}
          
                </div>

            </div>

            <div class="col-md-2">
                
                <div class="form-group">
                    
                    {!! Form::label('rip', 'RIP', array('class'=>'control-label')) !!}
                    {!! Form::text('rip', null, ['class'=>'form-control','id'=>'rip']) !!}
          
                </div>

            </div>


            <div class="col-md-6">
              
                <div class="form-group">
                    
                    {!! Form::label('endereco', 'Endereço', array('class'=>'control-label')) !!}
                    {!! Form::text('endereco', null, ['class'=>'form-control','id'=>'endereco']) !!}
          
                </div>

            </div>

            <div class="col-md-2">
                
                <div class="form-group">
                    
                    {!! Form::label('numero', 'Número', array('class'=>'control-label')) !!}
                    {!! Form::text('numero', null, ['class'=>'form-control','id'=>'numero']) !!}
          
                </div>

            </div>

            <div class="col-md-3">
                
                <div class="form-group">
                    
                    {!! Form::label('cidade', 'Cidade', array('class'=>'control-label')) !!}
                    {!! Form::text('cidade', null, ['class'=>'form-control','id'=>'cidade']) !!}
          
                </div>

            </div>

            <div class="col-md-1">
                
                <div class="form-group">
                    
                    {!! Form::label('uf', 'UF', array('class'=>'control-label')) !!}
                    {!! Form::text('uf', null, ['class'=>'form-control','id'=>'uf']) !!}
          
                </div>

            </div>

            <div class="col-md-4">
                
                <div class="form-group">
                    
                    {!! Form::label('cep', 'CEP', array('class'=>'control-label')) !!}
                    {!! Form::text('cep', null, ['class'=>'form-control','id'=>'cep']) !!}
          
                </div>


            </div>

            <div class="col-md-4">
                
                <div class="form-group">
                    
                    {!! Form::label('complemento', 'Complemento', array('class'=>'control-label')) !!}
                    {!! Form::text('complemento', null, ['class'=>'form-control','id'=>'complemento']) !!}
          
                </div>


            </div>

            <div class="col-md-4">
                
                <div class="form-group">
                    
                    {!! Form::label('bairro', 'Bairro', array('class'=>'control-label')) !!}
                    {!! Form::text('bairro', null, ['class'=>'form-control','id'=>'bairro']) !!}
          
                </div>


            </div>

            <div class="col-md-4">
                
                <div class="form-group">
                    
                    {!! Form::label('telefone', 'Telefone', array('class'=>'control-label')) !!}
                    {!! Form::text('telefone', null, ['class'=>'form-control','id'=>'telefone']) !!}
          
                </div>


            </div>

            <div class="col-md-4">
                
                <div class="form-group">
                    
                    {!! Form::label('responsavel', 'Responsável', array('class'=>'control-label')) !!}
                    {!! Form::text('responsavel', null, ['class'=>'form-control','id'=>'responsavel']) !!}
          
                </div>


            </div>

            <div class="col-md-4">
                
                <div class="form-group">
                    
                    {!! Form::label('email', 'E-mail', array('class'=>'control-label')) !!}
                    {!! Form::text('email', null, ['class'=>'form-control','id'=>'email']) !!}
          
                </div>


            </div>
      

      <div class="col-md-3">
                
                <div class="form-group">
                    
                    {!! Form::label('matriculaRI', 'Matricula RI', array('class'=>'control-label')) !!}
                    {!! Form::text('matriculaRI', null, ['class'=>'form-control','id'=>'matriculaRI']) !!}
          
                </div>


            </div>

            <div class="col-md-3">
                
                <div class="form-group">
                    
                    {!! Form::label('tipoImovel', 'Tipo do Imóvel', array('class'=>'control-label')) !!}
                    {!! Form::text('tipoImovel', null, ['class'=>'form-control','id'=>'tipoImovel']) !!}
          
                </div>


            </div>

            <div class="col-md-3">
                
                <div class="form-group">
                    
                    {!! Form::label('area', 'Área', array('class'=>'control-label')) !!}
                    {!! Form::text('area', null, ['class'=>'form-control','id'=>'area']) !!}
                   
                </div>


            </div>
            <div class="col-md-3">
                
                <div class="form-group">
                    
                    {!! Form::label('areaTerreno', 'Área do terreno', array('class'=>'control-label')) !!}
                    {!! Form::text('areaTerreno', null, ['class'=>'form-control input-group','id'=>'areaTerreno']) !!}
                    
                   
                </div>


            </div>
</div>

@section('js')


<script>
$(document).ready(function(){
    $("#dataInauguracao").datepicker();

  //Mascaras nos campos

  $("#cep").mask("00000-000");
  $("#telefone").mask("(00) 0000-0000");
  $('#cnpj').mask('00.000.000/0000-00', {reverse: true});

 


  // Adicionamos o evento onclick ao botão com o ID "pesquisar"
  $('#pesquisar').on('click', function(e) {
    
    // Apesar do botão estar com o type="button", é prudente chamar essa função para evitar algum comportamento indesejado
    e.preventDefault();
    
    // Aqui recuperamos o cnpj preenchido do campo e usamos uma expressão regular para limpar da string tudo aquilo que for diferente de números
    var cnpj = $('#cnpj').val().replace(/[^0-9]/g, '');
    
    // Fazemos uma verificação simples do cnpj confirmando se ele tem 14 caracteres
    if(cnpj.length == 14) {
    
      // Aqui rodamos o ajax para a url da API concatenando o número do CNPJ na url
      $.ajax({
        url:'https://www.receitaws.com.br/v1/cnpj/' + cnpj,
        method:'GET',
        dataType: 'jsonp', // Em requisições AJAX para outro domínio é necessário usar o formato "jsonp" que é o único aceito pelos navegadores por questão de segurança
        complete: function(xhr){
        
          // Aqui recuperamos o json retornado
          response = xhr.responseJSON;
          
          // Na documentação desta API tem esse campo status que retorna "OK" caso a consulta tenha sido efetuada com sucesso
          if(response.status == 'OK') {
          
            // Agora preenchemos os campos com os valores retornados
            $('#razaoSocial').val(formatText(response.nome));

            
            
            $('#nomeFantasia').val(formatText(response.fantasia));

            $('#endereco').val(formatText(response.logradouro));
            $('#numero').val(response.numero);
            $('#complemento').val(response.complemento);
            $('#cep').val(response.cep);
            $('#bairro').val(formatText(response.bairro));
            $('#cidade').val(formatText(response.municipio));
            $('#uf').val(response.uf);


            $('#telefone').val(response.telefone);
            $('#responsavel').val(response.responsavel);
            $('#email').val(response.email);





          
          // Aqui exibimos uma mensagem caso tenha ocorrido algum erro
          } else {
            alert(response.message); // Neste caso estamos imprimindo a mensagem que a própria API retorna
          }
        }
      });
    
    // Tratativa para caso o CNPJ não tenha 14 caracteres
    } else {
      alert('CNPJ inválido');
    }
  });
});


function formatText(text) {
    var loweredText = text.toLowerCase();
    var words = loweredText.split(" ");
    for (var a = 0; a < words.length; a++) {
        var w = words[a];

        var firstLetter = w[0];
        w = firstLetter.toUpperCase() + w.slice(1);

        words[a] = w;
    }
    return words.join(" ");
}

</script>


@stop