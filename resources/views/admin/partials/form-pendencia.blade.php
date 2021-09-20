<div class="box-body">
  
@if($errors->any())
    {!! implode('', $errors->all('<div class="callout callout-danger">:message</div>')) !!}
@endif


      <div class="col-md-3">
        <div class="form-group">
          
          {!! Form::label('servico_id', 'Ordem de serviço', array('class'=>'control-label')) !!}
        
          {!! Form::select('servico_id', $servico, null,['class'=>'form-control','id'=>'servico_id']) !!}
          
        </div>
      </div>

     

      <div class="col-md-6">
        <div class="form-group">
        {!! Form::label('pendencia', 'Descrição', array('class'=>'control-label')) !!}
        {!! Form::text('pendencia', null, ['class'=>'form-control','id'=>'pendencia']) !!}
        </div>
        
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('status', 'Status', array('class'=>'control-label')) !!}
        {!! Form::select('status', array('pendente' => 'Pendente', 'concluido' => 'Concluido'), null, ['class'=>'form-control','id'=>'status']) !!}
        </div>
    </div>

       <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('responsavel_tipo', 'Responsabilidade', array('class'=>'control-label')) !!}
        {!! Form::select('responsavel_tipo', array('usuario' => 'Castro', 'cliente' => 'Cliente','op'=>'Orgão Público'), null, ['class'=>'form-control','id'=>'responsavel_tipo']) !!}
        </div>
        
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('responsavel_id', 'Responsável', array('class'=>'control-label')) !!}
        @if(Route::is('pendencia.create'))
        {!! Form::select('responsavel_id', $responsaveis , Auth::id(), ['class'=>'form-control','id'=>'responsavel_id']) !!}
        @else
        {!! Form::select('responsavel_id', $responsaveis , null, ['class'=>'form-control','id'=>'responsavel_id']) !!}
        @endif
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
        {!! Form::label('vencimento', 'Data limite', array('class'=>'control-label')) !!}
        {!! Form::text('vencimento' , null, ['class'=>'form-control','id'=>'vencimento','data-date-format'=>'dd/mm/yyyy']) !!}
        </div>
      </div>

      
      <div class="col-md-6" id="vinculos">
        <label for="vinculo" class="control-label">Vínculo</label>
        <div class="input-group control-group after-add-more">  
          {!! Form::select('vinculo', $vinculo, null,['class'=>'form-control','id'=>'vinculo','name'=>'vinculo[]']) !!}
          <div class="input-group-btn">   
            <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Adicionar</button>  
          </div>  
        </div> 
        
        <div class="copy hide">  
          <div class="control-group input-group" style="margin-top:10px">  
            {!! Form::select('vinculo', $vinculo, null,['class'=>'form-control','id'=>'vinculo_copy','name'=>'vinculo[]']) !!}
            <div class="input-group-btn">   
              <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remover</button>  
            </div>  
          </div>  
        </div>
        
        @if(!Route::is('pendencia.create'))
          @foreach($vinculos as $key => $v)
          <div class="control-group input-group" style="margin-top:10px">  
            {!! Form::select('vinculo', $vinculo, $key,['class'=>'form-control','id'=>'vinculo','name'=>'vinculo[]']) !!}
            <div class="input-group-btn">   
              <button class="btn btn-danger remove" type="button" onClick="removerVinculo({{$pendencia->id}},{{$key}})"><i class="glyphicon glyphicon-remove"></i> Remover</button>  
            </div>  
          </div>  

          @endforeach
        @endif
  
      </div>


      <div class="col-md-12">
        <div class="form-group">
           {!! Form::label('observacoes', 'Observações', array('class'=>'control-label')) !!}
           {!! Form::textarea('observacoes', null, ['class'=>'form-control','id'=>'observacoes']) !!}
        </div>
      </div>
     
   

</div>



@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	
	$(document).ready(function() {

    var vinculo = {!! $pendencia->vinculo !!}

    console.log(vinculo);
   
   
  if(!vinculo)
    {
      $('#vinculo').select2();
      $('#vinculo').val(null).trigger('change');
    }


    $( "#removerVinculo" ).click(function() {
     $('#vinculo').val(null).trigger('change');
   });


    $('#responsavel_id').select2();
    
    
   

        
    

});
</script>
@endif

@if(Route::is('pendencia.create'))
<script>
   $(document).ready(function() {
    $('#vinculo').select2();
    $('#vinculo').val(null).trigger('change');
   });
</script>
@endif

<script>
  $(document).ready(function() {
             
    var Today = new Date();

    $("#vencimento").datepicker({
      defaultDate:Today,
      showButtonPanel:true,
      todayHighlight: true,

});


  });

  function removerVinculo(id)
    {
      console.log("RemoverVinculo")
      var pendenciaID = id;
      
      $.ajax({
                url: '{{url('admin/pendencia/removerVinculo')}}/'+pendenciaID+'',
                method: 'GET',
                success: function(data) {

                  $(this).data('status', data.completed);    
                  console.log({!!json_encode($vinculo)!!}); 
                  
                  $('#vinculo').select2();
                                    
                  },
                })
    
    }
</script>
@endif


@stop