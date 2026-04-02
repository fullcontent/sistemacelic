<div class="box-body">

  
    {!! Form::hidden('user_id', Auth::id(), ['class'=>'form-control']) !!}
    {!! Form::hidden('servico_id', optional($servico)->id, ['class'=>'form-control', 'id' => 'hidden_servico_id']) !!}

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('prestador_id', 'Prestador ID') !!}

            

            @if(Route::is('ordemServico.edit'))
            {!! Form::select('prestador_id', $prestadores, optional($ordemServico->prestador)->id, ['class'=>'form-control']) !!}

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

            @if(Route::is('ordemServico.edit'))
            {!! Form::text('telefone', optional($ordemServico->prestador)->telefone, ['class'=>'form-control']) !!}
            @else
            {!! Form::text('telefone', null, ['class'=>'form-control']) !!}
            @endif
           
        </div>
    </div>


    
    @if(Route::is('ordemServico.edit') && $ordemServico->pagamentos->count() > 0)
        @foreach($ordemServico->pagamentos as $index => $pagamento)
        <div class="col-md-12 parcela-edit" id="parcela_{{$index}}" data-id="{{$index + 1}}">
            <div class="form-group">
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('valorParcela[]', 'Valor da parcela') !!}
                        {!! Form::text('valorParcela[]', $pagamento->valor, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('dataVencimento[]', 'Vencimento') !!}
                        {!! Form::text('dataVencimento[]', $pagamento->dataVencimento ? \Carbon\Carbon::parse($pagamento->dataVencimento)->format('d/m/Y') : null, ['class'=>'form-control datepicker', 'data-date-format'=>'dd/mm/yyyy', 'autocomplete'=>'off']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('dataPagamento[]', 'Pagamento') !!}
                        {!! Form::text('dataPagamento[]', $pagamento->dataPagamento ? \Carbon\Carbon::parse($pagamento->dataPagamento)->format('d/m/Y') : null, ['class'=>'form-control datepicker', 'data-date-format'=>'dd/mm/yyyy', 'autocomplete'=>'off']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('obs[]', 'Observações') !!}
                        {!! Form::text('obs[]', $pagamento->obs, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('comprovante[]', 'Comprovante') !!}
                        @if($pagamento->comprovante)
                            <a href="{{ url("public/uploads/$pagamento->comprovante") }}" target="_blank" class="btn btn-xs btn-success">Ver atual</a>
                            {!! Form::hidden('comprovante_atual[]', $pagamento->comprovante) !!}
                        @else
                            {!! Form::hidden('comprovante_atual[]', null) !!}
                        @endif
                        {!! Form::file('comprovante[]', ['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
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
                        {!! Form::text('dataVencimento[]', null, ['class'=>'form-control datepicker', 'data-date-format'=>'dd/mm/yyyy', 'autocomplete'=>'off']) !!}
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        {!! Form::label('dataPagamento[]', 'Pagamento') !!}
                        {!! Form::text('dataPagamento[]', null, ['class'=>'form-control datepicker', 'data-date-format'=>'dd/mm/yyyy', 'autocomplete'=>'off']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('obs[]', 'Observações') !!}
                        {!! Form::text('obs[]', null, ['class'=>'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('comprovante[]', 'Comprovante') !!}
                        {!! Form::file('comprovante[]', ['class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-md-12" id="dadosPagamento" style="display: none;">
        <div class="callout callout-info" style="margin-top: 15px;">
            <h4><i class="fa fa-info-circle"></i> Dados de Pagamento do Prestador</h4>
            <div class="row">
                <div class="col-md-4" id="prestadorFormaPagamento">
                    <strong>Forma de pagamento:</strong> <span style="font-size: 16px;">PIX</span>
                </div>
                <div class="col-md-4" id="prestadorTipoChave">
                    <strong>Tipo de chave:</strong> <span style="font-size: 16px;">CPF/CNPJ</span>
                </div>
                <div class="col-md-4" id="prestadorChavePix">
                    <strong>Chave PIX:</strong> <span style="font-size: 16px;">Chave PIX</span>
                </div>
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

        <div class="col-md-12" id="servicoPrincipal" @if(!$servico) style="display: none;" @endif>
            <div class="form-group">
                <div class="col-md-7">
                    <div class="form-group">
                        {!! Form::label('servicoPrincipal_id', 'Serviço Principal') !!}
                        
                        @if($servico)
                            {!! Form::text('servicoPrincipal_nome', ($servico->os ?? '---').' | '.(optional($servico->empresa)->nomeFantasia ?? '---').' | '.(optional($servico->unidade)->codigo ?? '---').' | '.(optional($servico->unidade)->nomeFantasia ?? '---'), ['class'=>'form-control', 'readonly']) !!}
                            {!! Form::hidden('servicoPrincipal_id', $servico->id, ['class'=>'form-control', 'id' => 'servicoPrincipal_id']) !!}
                        @else
                            {!! Form::select('servicoPrincipal_id', [], null, ['class'=>'form-control select2-servico-ajax', 'id' => 'servicoPrincipal_id']) !!}
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('servicoPrincipal_valor', 'Valor') !!}
                        {!! Form::text('servicoPrincipal_valor', Route::is('ordemServico.edit') ? optional($vinculoPrincipal)->valor : null, ['class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-md-1">
                    <div class="form-group">
                        {!! Form::label('servicoPrincipal_reembolso', 'reembolso') !!}
                        {!! Form::select('servicoPrincipal_reembolso', ['nao'=>'Não','sim'=>'Sim'] , Route::is('ordemServico.edit') ? optional($vinculoPrincipal)->reembolso : null, ['class'=>'form-control','id'=>'servicoPrincipal_reembolso']) !!}
                    </div>
                </div>



            </div>
        </div>

        <div class="col-md-12" id="servicoVinculado" style="display: none;">
            <div class="form-group">
                <div class="col-md-7">
                    <div class="form-group">
                        {!! Form::label('servicoVinculado_id[]', 'Serviço Vinculado') !!}
                        <select name="servicoVinculado_id[]" class="form-control select2-servico-ajax" style="width: 100%" disabled></select>
                        {!! Form::hidden('servicoVinculado_nome[]', '---', ['class'=>'form-control', 'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('servicoVinculado_valor[]', 'Valor') !!}
                        {!! Form::text('servicoVinculado_valor[]', null, ['class'=>'form-control', 'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        {!! Form::label('servicoVinculado_reembolso[]', 'reembolso') !!}
                        {!! Form::select('servicoVinculado_reembolso[]', [null,'nao'=>'Não','sim'=>'Sim'] ,null, ['class'=>'form-control', 'disabled']) !!}

                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger form-control removerServicoVinculado"><i class="fa fa-trash"></i></button>
                    </div>
                </div>


            </div>
        </div>



        <div id="clonedDivsContainer">
            @if(Route::is('ordemServico.edit'))
                @foreach($vinculoOutros as $vinculo)
                <div class="col-md-12 servicoVinculado-edit" id="servicoVinculado_{{$vinculo->id}}">
                    <div class="form-group">
                        <div class="col-md-7">
                            <div class="form-group">
                                {!! Form::label('servicoVinculado_id[]', 'Serviço Vinculado') !!}
                                <select name="servicoVinculado_id[]" class="form-control select2-servico-ajax" style="width: 100%">
                                    <option value="{{$vinculo->servico_id}}" selected>{{$vinculo->servico->os}} | {{$vinculo->servico->nome}}</option>
                                </select>
                                {!! Form::hidden('servicoVinculado_nome[]', '---', ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('servicoVinculado_valor[]', 'Valor') !!}
                                {!! Form::text('servicoVinculado_valor[]', $vinculo->valor, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                {!! Form::label('servicoVinculado_reembolso[]', 'reembolso') !!}
                                {!! Form::select('servicoVinculado_reembolso[]', [null,'nao'=>'Não','sim'=>'Sim'], $vinculo->reembolso, ['class'=>'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger form-control removerServicoVinculado"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

    </div>



</div>

@section('js')
<script>
$(document).ready(function () {



    const route = "{{Route::current()->getName()}}";

    $('.select2').select2({
        placeholder: 'Selecione uma opção...',
        allowClear: true
    });

    // Inicializa busca AJAX no serviço principal se ele for um select
    if ($('#servicoPrincipal_id').is('select')) {
        initSelect2Servico($('#servicoPrincipal_id'));
    }

    // Se mudar o serviço principal no caso de OS Avulsa, sincroniza o servico_id principal
    $('#servicoPrincipal_id').on('change', function() {
        $('#hidden_servico_id').val($(this).val());
    });
   
    function initSelect2Servico($el) {
        $el.select2({
            placeholder: 'Digite OS ou Nome do serviço...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('api.servico.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.os + ' | ' + item.nome + (item.unidade ? ' | ' + item.unidade.nomeFantasia : ''),
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    }

    // Datepicker plugin
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            language: 'pt-BR'
        });
    }

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
            $clonedParcela.find('.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind();
            $parcelaDiv.after($clonedParcela);

         // Find all form elements inside the cloned div and log their ids
            $clonedParcela.find(':input').each(function() {
                var oldId = $(this).attr('id');
                var idArray = oldId.split('[');
                idArray.splice(1, 0, i);
                var newIdValue = idArray.join('[');


                console.log(oldId)
            });

            if ($.fn.datepicker) {
                $clonedParcela.find('.datepicker').datepicker({
                    autoclose: true,
                    format: 'dd/mm/yyyy',
                    language: 'pt-BR'
                });
            }
        }
    });

    

    $('#cloneButton').click(function(event) {
        event.preventDefault();

        // Se o principal estiver escondido (OS Avulsa inicial), apenas mostra ele
        if ($('#servicoPrincipal').is(':hidden')) {
            $('#servicoPrincipal').show();
            if ($('#servicoPrincipal_id').is('select')) {
                initSelect2Servico($('#servicoPrincipal_id'));
            }
            recalcularRateioAutomatico();
            return;
        }

        var clonedDiv = $('#servicoVinculado').clone();
        
        // Remove ids that might conflict and fix visibility
        clonedDiv.attr('id', 'servicoVinculado_' + Date.now());
        clonedDiv.show();
        clonedDiv.find(':input').prop('disabled', false);

        // Remove select2 spans if they were cloned
        clonedDiv.find('.select2-container').remove();
        clonedDiv.find('select').removeClass('select2-hidden-accessible').next().remove();

        $('#clonedDivsContainer').append(clonedDiv);

        // Init select2 on the new element
        initSelect2Servico(clonedDiv.find('.select2-servico-ajax'));

        recalcularRateioAutomatico();
  });

  function recalcularRateioAutomatico() {
      var valorTotalStr = $('#valorServico').val() || "0";
      var valorTotalNum = parseFloat(valorTotalStr.replace(',', '.')) || 0;
      
      // Contagem: principal (se visível) + vinculados extras
      var principalCount = $('#servicoPrincipal').is(':visible') ? 1 : 0;
      var vinculadosCount = $('#clonedDivsContainer .servicoVinculado-edit, #clonedDivsContainer div[id^="servicoVinculado_"]').length;
      var count = principalCount + vinculadosCount;

      if (count === 0) return;
      
      var valorRateado = (valorTotalNum / count).toFixed(2);
      var valorRateadoStr = valorRateado.replace('.', ',');

      // Atualiza Principal
      $('#servicoPrincipal_valor').val(valorRateadoStr);
      
      // Atualiza Vinculados
      $('#clonedDivsContainer input[name="servicoVinculado_valor[]"]').val(valorRateadoStr);
      
      calcularTotaisVinculados();
  }

  function calcularTotaisVinculados() {
      var valorTotalStr = $('#valorServico').val() || "0";
      var valorTotal = parseFloat(valorTotalStr.replace(',', '.')) || 0;
      
      var valorPrincipalStr = $('#servicoPrincipal_valor').val() || "0";
      var valorPrincipal = parseFloat(valorPrincipalStr.replace(',', '.')) || 0;
      
      var somaVinculados = 0;
      $('#clonedDivsContainer input[name="servicoVinculado_valor[]"]').each(function() {
          var valStr = $(this).val() || "0";
          somaVinculados += parseFloat(valStr.replace(',', '.')) || 0;
      });

      var somaGeral = valorPrincipal + somaVinculados;
      
      if (somaGeral > (valorTotal + 0.01)) {
          $('#servicoPrincipal_valor').css('border-color', 'red');
          $('#clonedDivsContainer input[name="servicoVinculado_valor[]"]').css('border-color', 'red');
          $('button[type="submit"]').prop('disabled', true);
          
          if ($('#erroSomaVinculados').length === 0) {
              $('.servicos').append('<p id="erroSomaVinculados" style="color:red; font-weight:bold; margin-top:10px;">A soma dos serviços vinculados ('+somaGeral.toFixed(2)+') não pode ultrapassar o valor total ('+valorTotal.toFixed(2)+').</p>');
          } else {
              $('#erroSomaVinculados').html('A soma dos serviços vinculados ('+somaGeral.toFixed(2)+') não pode ultrapassar o valor total ('+valorTotal.toFixed(2)+').').show();
          }
      } else {
          $('#servicoPrincipal_valor').css('border-color', '#d2d6de');
          $('#clonedDivsContainer input[name="servicoVinculado_valor[]"]').css('border-color', '#d2d6de');
          $('button[type="submit"]').prop('disabled', false);
          $('#erroSomaVinculados').hide();
      }
  }

  $(document).on('keyup', '#servicoPrincipal_valor, input[name="servicoVinculado_valor[]"]', function() {
      calcularTotaisVinculados();
  });

  $(document).on('click', '.removerServicoVinculado', function() {
      $(this).closest('div[id^="servicoVinculado"]').remove();
      recalcularRateioAutomatico();
  });

  $('#valorServico').keyup(function(){
    var valorServicoStr = $(this).val() || "0";
    var valorServicoNum = parseFloat(valorServicoStr.replace(',', '.')) || 0;
    var numParcelas = parseInt($('#formaPagamento').val()) || 1;
    
    // Atualiza automaticamente o valor do serviço principal vinculado
    recalcularRateioAutomatico();

    if (numParcelas == 1) {
        $('input[id="valorParcela[]"]').val(valorServicoStr);
    }
    if(numParcelas > 1) {
        $('input[id="valorParcela[]"]').val((valorServicoNum/numParcelas).toFixed(2));
    }
    
    calcularTotaisVinculados();
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
            $('#prestadorFormaPagamento span').html(response.formaPagamento)
            $('#prestadorTipoChave span').html(response.tipoChave)
            $('#prestadorChavePix span').html(response.chavePix)
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

  if(route == 'ordemServico.criar')
  {
    $("#prestador_id").val(null).change();
    $("#dadosPagamento").hide();
    
  } else if (route == 'ordemServico.edit') {
    $('.select2-servico-ajax').each(function() {
        initSelect2Servico($(this));
    });
    // Trigger prestador info load
    $('#prestador_id').change();
  }
    

});

  </script>

@endsection