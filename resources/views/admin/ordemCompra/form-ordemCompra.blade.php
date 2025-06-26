<div class="box-body">

  
    {!! Form::hidden('user_id', Auth::id(), ['class'=>'form-control']) !!}
    {!! Form::hidden('servico_id', $servico->id, ['class'=>'form-control']) !!}

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('prestador_id', 'Prestador ID') !!}

            

            @if(Route::is('ordemCompra.edit'))
            {!! Form::select('prestador_id', $prestadores, $ordemCompra->prestador->id, ['class'=>'form-control']) !!}

            @else
            {!! Form::select('prestador_id', $prestadores, null, ['class'=>'form-control']) !!}
            @endif
            
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('valorServico', 'Valor do Serviço') !!}
            {!! Form::text('valorServico', null, ['class'=>'form-control']) !!}
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('formaPagamento', 'Forma de Pagamento') !!}
            {!! Form::select('formaPagamento', ['1' => 'A vista', '2' => '2x', '3' => '3x', '4' => '4x', '5' => '5x'], null, ['class' => 'form-control']) !!}

        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('telefone', 'Telefone') !!}

            @if(Route::is('ordemCompra.edit'))
            {!! Form::text('telefone', $ordemCompra->prestador->telefone, ['class'=>'form-control']) !!}
            @else
            {!! Form::text('telefone', null, ['class'=>'form-control']) !!}
            @endif
           
        </div>
    </div>


    
    <div class="col-md-12" id="parcela" data-id="1">
        <div class="form-group">
            
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('valorParcela[]', 'Valor da parcela') !!}
                    {!! Form::text('valorParcela[]', null, ['class'=>'form-control','data-id'=>1]) !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('dataVencimento[]', 'Vencimento') !!}
                    {!! Form::text('dataVencimento[]', null, ['class'=>'form-control','data-id'=>1]) !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('dataPagamento[]', 'Pagamento') !!}
                    {!! Form::text('dataPagamento[]', null, ['class'=>'form-control','data-id'=>1]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('obs[]', 'Observações') !!}
                    {!! Form::text('obs[]', null, ['class'=>'form-control','data-id'=>1]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('comprovante[]', 'Comprovante') !!}
                    {!! Form::file('comprovante[]', null, ['class'=>'form-control','data-id'=>1]) !!}
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-12" id="dadosPagamento">
        <div class="form-group">
            <p><b>Dados de pagamento</b></p>
        </div>

        <div class="col-md-4">
            <div class="form-group" id="prestadorFormaPagamento">
                <p><b>Forma de pagamento</b></p>
                <h2>PIX</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group" id="prestadorTipoChave">
                <p><b>Tipo de chave</b></p>
                <h2>CPF/CNPJ</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group" id="prestadorChavePix">
                <p><b>Chave PIX</b></p>
                <h2 >Chave PIX</h2>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('escopo', 'Escopo') !!}
            {!! Form::textarea('escopo', null, ['class'=>'form-control']) !!}
        </div>
    </div>  

<h4>Serviços vinculados</h4>


<div class="col-md-1">
    <div class="form-group">
        <button id="cloneButton" class="btn btn-success"><i class="fa fa-plus"></i> Adicionar</button>
    </div>
</div>
    <div class="servicos">

        <div class="col-md-12" id="servicoPrincipal">
            <div class="form-group">
                <div class="col-md-7">
                    <div class="form-group">
                        {!! Form::label('servicoPrincipal_nome', 'Serviço') !!}
                        {!! Form::text('servicoPrincipal_nome', $servico->os.' | '.$servico->empresa->nomeFantasia.' | '.$servico->unidade->codigo.' | '.$servico->unidade->nomeFantasia, ['class'=>'form-control']) !!}
                        
                        {!! Form::hidden('servicoPrincipal_id', $servico->id, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('servicoPrincipal_valor', 'Valor') !!}
                        {!! Form::text('servicoPrincipal_valor', null, ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                        {!! Form::label('servicoPrincipal_reembolso', 'reembolso') !!}
                        {!! Form::select('servicoPrincipal_reembolso', ['nao'=>'Não','sim'=>'Sim'] ,null, ['class'=>'form-control','id'=>'servicoPrincipal_reembolso']) !!}

                    </div>
                </div>



            </div>
        </div>

        <div class="col-md-12" id="servicoVinculado" style="display: none;">
            <div class="form-group">
                <div class="col-md-7">
                    <div class="form-group">
                        {!! Form::label('servicoVinculado_nome[]', 'Serviço Vinculado') !!}
                        {!! Form::text('servicoVinculado_nome[]', null, ['class'=>'form-control']) !!}
                        
                        {!! Form::hidden('servicoVinculado_id[]', null, ['class'=>'form-control']) !!}

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('servicoVinculado_valor[]', 'Valor') !!}
                        {!! Form::text('servicoVinculado_valor[]', null, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        {!! Form::label('servicoVinculado_reembolso[]', 'reembolso') !!}
                        {!! Form::select('servicoVinculado_reembolso[]', [null,'nao'=>'Não','sim'=>'Sim'] ,null, ['class'=>'form-control']) !!}

                    </div>
                </div>


            </div>
        </div>



        <div id="clonedDivsContainer">
            <!-- Cloned <div> elements will be inserted here -->
        </div>

    </div>



</div>

@section('js')
<script>
$(document).ready(function () {



    const route = "{{Route::current()->getName()}}";
   
    // Get the select element and the parcela div
    var $parcelaDiv = $('#parcela');
    var $numParcelasSelect = $('#formaPagamento');

    // When the form payment method is changed, clone or remove the parcela divs
    $numParcelasSelect.change(function () {

        // Get the number of installments
        var numParcelas = parseInt($(this).val());


        if(numParcelas)
        {
            var valorServico  = $('#valorServico').val();

            $('input[id="valorParcela[]"]').val(parseFloat(valorServico/numParcelas));

        }

        // Remove existing cloned parcela divs
        $('.parcela-cloned').remove();

        // Clone the parcela div for each installment
        for (var i = 1; i < numParcelas; i++) {
            var $clonedParcela = $parcelaDiv.clone();
            // Add class to cloned div so we can remove it later if needed
            $clonedParcela.addClass('parcela-cloned');
            $parcelaDiv.after($clonedParcela);

         // Find all form elements inside the cloned div and log their ids
            $clonedParcela.find(':input').each(function() {
                var oldId = $(this).attr('id');
                var idArray = oldId.split('[');
                idArray.splice(1, 0, i);
                var newIdValue = idArray.join('[');


                console.log(oldId)
            });

        }
    });

    


    $('#cloneButton').click(function(event) {
        event.preventDefault();

        $('#servicoVinculado').show();

        const servicos = document.querySelectorAll('#servicoVinculado');         
        const numberOfServicos = servicos.length;

        var clonedDiv = $('#servicoVinculado').clone();
        

        $('#clonedDivsContainer').append(clonedDiv);

       
  });

  
  $('#valorServico').keyup(function(){
    var numParcelas = parseInt($('#formaPagamento').val());
    if (numParcelas == 1) {
        var valorServico = $(this).val();
        $('input[id="valorParcela[]"]').val(valorServico);
        $('input[id="servicoPrincipal_valor"]').val(valorServico);
    }
    if(numParcelas > 1)
    {
        var valorServico = $(this).val();
        $('input[id="valorParcela[]"]').val(parseFloat(valorServico/numParcelas));
    }
  })

  
  


  $('#prestador_id').select2();
  $('#prestador_id').change(function() {
    var prestadorId = $(this).val();

    $.ajax({
      url: '/api/getPrestadorInfo', // Replace with actual URL of your server-side script
      method: 'GET',
      data: { prestador_id: prestadorId },
      dataType: 'json',
      success: function(response) {
        
        $('#telefone').val(response.telefone);
        
        if(response.formaPagamento == 'pix')
        {   
            $("#dadosPagamento").show();
            $('#prestadorFormaPagamento h2').html(response.formaPagamento)
            $('#prestadorTipoChave h2').html(response.tipoChave)
            $('#prestadorChavePix h2').html(response.chavePix)
        }

        if(response.formaPagamento == 'deposito')
        {   
            $("#dadosPagamento").hide();



        }

        if(response.formaPagamento == 'boleto')
        {   
            $("#dadosPagamento").hide();

            

        }
        
       
       
      },
      error: function(xhr) {
        alert('Error: ' + xhr.responseText);
      }
    });
  });

  if(route == 'ordemCompra.criar')
  {
    $("#prestador_id").val(null).change();
    $("#dadosPagamento").hide();
    
  }
    

});

  </script>

@endsection