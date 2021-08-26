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

      <div class="col-md-3">
        <div class="form-group">
          
          {!! Form::label('vinculo', 'Vinculado a OS', array('class'=>'control-label')) !!}
        
          @if(!$pendencia->vinculo)
         
          {!! Form::select('vinculo', $vinculo, null,['class'=>'form-control','id'=>'vinculo']) !!}
          <a href="#" class="link"  id="removerVinculo" onClick="removerVinculo({{$pendencia->id}})">Remover</a>
          
          @else
         
          {!! Form::select('vinculo', $vinculo, null,['class'=>'form-control','id'=>'vinculo']) !!}
          <a href="#" class="link" id="removerVinculo" onClick="removerVinculo({{$pendencia->id}})">Remover</a>
          @endif
         
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


    
    
     

    $('#responsavel_id').select2();
    

    $( "#removerVinculo" ).click(function() {
     
      $('#vinculo').val(null).trigger('change');
    });


    


    // Get users 'today' date
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
                 
                  
                  $('#vinculo').select2();
                                    
                  },
                })
    
    }

</script>

@stop