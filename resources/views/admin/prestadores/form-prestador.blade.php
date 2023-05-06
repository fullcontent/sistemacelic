<div class="box-body">

    <div class="col-md-4">
        <div class="form-group">

            {!! Form::label('nome', 'Nome', array('class'=>'control-label')) !!}
            {!! Form::text('nome', null, ['class'=>'form-control','id'=>'nome']) !!}

        </div>

    </div>

    <div class="col-md-3">
        <div class="form-group">

            {!! Form::label('qualificacao','Qualificação', array('class'=>'control-label')) !!}
            {!! Form::select('qualificacao', [
            'Arquiteto (a)' => 'Arquiteto (a)',
            'Contador (a)' => 'Contador (a)',
            'Engenheiro (a)' => 'Engenheiro (a)',
            'Office boy' => 'Office boy',
            'Projetista' => 'Projetista',
            'Outros' => 'Outros'
            ], null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">

            {!! Form::label('cnpj', 'CNPJ/CPF', array('class'=>'control-label')) !!}
            {!! Form::text('cnpj', null, ['class'=>'form-control','id'=>'cnpj']) !!}

        </div>

    </div>

    <div class="col-md-2">
        <div class="form-group">

            {!! Form::label('telefone', 'Telefone', array('class'=>'control-label')) !!}
            {!! Form::text('telefone', null, ['class'=>'form-control','id'=>'telefone']) !!}

        </div>

    </div>

    <div class="col-md-12">
        <div class="form-group">

            {!! Form::label('email', 'E-Mail', array('class'=>'control-label')) !!}
            {!! Form::text('email', null, ['class'=>'form-control','id'=>'email']) !!}

        </div>

    </div>

    <div class="col-md-2">
        <div class="form-group">

            {!! Form::label('formaPagamento', 'Forma de Pagamento', array('class'=>'control-label')) !!}
            {!! Form::select('formaPagamento', array('pix' => 'PIX','deposito'=>'Depósito', 'boleto'=>'Boleto'),null,
            ['class'=>'form-control','id'=>'formaPagamento']) !!}

        </div>

    </div>

    <div class="col-md-2">
        <div class="form-group">

            {!! Form::label('tipoChave', 'Tipo de Chave', array('class'=>'control-label')) !!}
            {!! Form::select('tipoChave', array('cnpj' => 'CPF/CNPJ','email'=>'E-mail',
            'celular'=>'Celular','aleatoria'=>'Chave Aleatória'),null, ['class'=>'form-control','id'=>'tipoChave']) !!}

        </div>

    </div>

    <div class="col-md-8">
        <div class="form-group">

            {!! Form::label('chavePix', 'Chave', array('class'=>'control-label')) !!}
            {!! Form::text('chavePix', null, ['class'=>'form-control','id'=>'chavePix']) !!}
        </div>

    </div>

    <div class="col-md-3">
        <div class="form-group">

            {!! Form::label('banco', 'Banco', array('class'=>'control-label')) !!}
            {!! Form::text('banco', null, ['class'=>'form-control','id'=>'banco']) !!}

        </div>

    </div>

    <div class="col-md-3">
        <div class="form-group">

            {!! Form::label('agencia', 'Agencia', array('class'=>'control-label')) !!}
            {!! Form::text('agencia', null, ['class'=>'form-control','id'=>'agencia']) !!}

        </div>

    </div>

    <div class="col-md-3">
        <div class="form-group">

            {!! Form::label('conta', 'conta', array('class'=>'control-label')) !!}
            {!! Form::text('conta', null, ['class'=>'form-control','id'=>'conta']) !!}

        </div>

    </div>


    <div class="col-md-12">

        <div class="col-md-6">
            <div class="form-group">

                {!! Form::label('tomadorNome', 'Nome do Tomador', array('class'=>'control-label')) !!}
                {!! Form::text('tomadorNome', null, ['class'=>'form-control','id'=>'tomadorNome']) !!}

            </div>

        </div>

        <div class="col-md-6">
            <div class="form-group">

                {!! Form::label('tomadorCnpj', 'CPF/CNPJ do Tomador', array('class'=>'control-label')) !!}
                {!! Form::text('tomadorCnpj', null, ['class'=>'form-control','id'=>'tomadorCnpj']) !!}

            </div>

        </div>

    </div>





    <div class="col-md-3">


        <div class="form-group">

            {!! Form::label('ufAtuacao', 'UF', array('class'=>'control-label')) !!}

            @if(Route::is('prestador.create'))
            {!! Form::select('ufAtuacao[]', array() ,null, ['class'=>'form-control','id'=>'ufAtuacao']) !!}
            @else
            {!! Form::select('ufAtuacao[]', array() ,$prestador->ufAtuacao, ['class'=>'form-control','id'=>'ufAtuacao'])
            !!}
            @endif

        </div>


    </div>
    <div class="col-md-4">


        <div class="form-group">

            {!! Form::label('cidadeAtuacao', 'Cidade', array('class'=>'control-label')) !!}


            @if(Route::is('prestador.create'))
            {!! Form::select('cidadeAtuacao[]', array() ,null, ['class'=>'form-control','id'=>'cidadeAtuacao'])!!}
            @else
            {!! Form::select('cidadeAtuacao[]', array() ,$prestador->cidadeAtuacao,
            ['class'=>'form-control','id'=>'cidadeAtuacao'])!!}
            @endif
        </div>





    </div>

<div class="col-md-12">
  <div class="form-group">
    {!! Form::label('obs', 'Observações', array('class'=>'control-label')) !!}
    {!! Form::textarea('obs', null, ['class'=>'form-control','id'=>'obs']) !!}
  </div>
</div>


</div>


@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Import jQuery library -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
$(document).ready(function() {

    const route = "{{Route::current()->getName()}}";
    const ufAtuacao = "{{$prestador->ufAtuacao}}";
    const cidadeAtuacao = "{{$prestador->cidadeAtuacao}}";
   


  




    $.ajax({
    url: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados',
    dataType: 'json',
    success: function(data) {
      var options = ''; // Create an empty string to store our select options
      data.sort(function(a, b) { // Sort the data array based on the "nome" property
        return a.nome.localeCompare(b.nome);
     });
      $.each(data, function(i, state) {
        options += '<option value="' + state.sigla.toLowerCase() + '">' + state.nome + '</option>'; // Build each option tag and append it to the options string
      });
      
      if(route != 'prestador.edit')
      {
        $('#ufAtuacao').empty().append('<option value="">Selecione o UF</option>'); // Clear and reset the city select list
      }
      
      
      
      $('#ufAtuacao').append(options); // Append all the options to the select element
    
      $('#ufAtuacao option[value="'+ufAtuacao+'"]').attr("selected", "selected").change();


    }
  });
  
  $('#ufAtuacao').on('change', function() { // Listen for changes in the state select list
    var stateCode = $(this).val(); // Get the selected state code
    if (stateCode) { // If a state is selected
      $.ajax({
        url: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/' + stateCode + '/municipios', // URL of the API endpoint
        dataType: 'json',
        success: function(data) {
            data.sort(function(a, b) { // Sort the data array based on the "nome" property
            return a.nome.localeCompare(b.nome);
            });

          $('#cidadeAtuacao').select2({
            placeholder: 'Selecione a cidade',
	          allowClear: true,
	          multiple: true,
          });


          $('#cidadeAtuacao').empty().append('<option value="">Selecione a cidade</option>'); // Clear and reset the city select list
          
      
          $.each(data, function(i, city) { // Loop through all cities
            $('#cidadeAtuacao').append($('<option>', { // Append each city as an option to the city select list
              value: city.nome,
              text : city.nome
            }));
          });
          $('#cidadeAtuacao').prop('disabled', false); // Enable the city select list
        }
      });
    } else {
      $('#cidadeAtuacao').empty().append('<option value="">Selecione um estado</option>').prop('disabled', true); // Reset and disable the city select list
    }


  });



  if($('#formaPagamento').val = 'pix')
  {
    $('#banco').prop('disabled', true); // Enable the city select list
    $('#agencia').prop('disabled', true); // Enable the city select list
    $('#conta').prop('disabled', true); // Enable the city select list
  }

  $("#formaPagamento").change(function(){
  
    switch($('#formaPagamento').val()) {
      case 'pix':
        console.log('forma pix');
        $('#tipoChave').prop('disabled', false); // Enable the city select list
        $('#chavePix').prop('disabled', false); // Enable the city select list
        break;
      case 'deposito':
        console.log('forma deposito');
        $('#tipoChave').val(null);
        $('#tipoChave').prop('disabled', true); // Enable the city select list
        $('#chavePix').prop('disabled', true); // Enable the city select list

        $('#banco').prop('disabled', false); // Enable the city select list
        $('#agencia').prop('disabled', false); // Enable the city select list
        $('#conta').prop('disabled', false); // Enable the city select list
        break;
      case 'boleto':
        console.log('forma boleto');
        $('#tipoChave').val(null);
        $('#tipoChave').prop('disabled', true); // Enable the city select list
        $('#chavePix').prop('disabled', true); // Enable the city select list
        $('#banco').prop('disabled', true); // Enable the city select list
        $('#agencia').prop('disabled', true); // Enable the city select list
        $('#conta').prop('disabled', true); // Enable the city select list
        break;
      
    }

  
  });


    



});
</script>
@stop
