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

@if(Route::is('pendencia.edit'))
<script>
var vinculo = {!! $pendencia->vinculos !!}
console.log(vinculo.length);
if(vinculo.length != 0)
  {
    $('#vencimento').prop('disabled',true);
    $('#vencimento').val(null);
  }
</script>
@endif

<script>
  
$(document).ready(function() {
    
    $('#vinculo').select2({
    placeholder: "Selecione um serviço",
    allowClear: true,
    minimumResultsForSearch: -1,
    templateResult: hideSelected,
    });
    $('#vinculo').val(null).change();
    $('#vinculo_copy').val(null).change();

    $('#vinculo').change(function(e){
      $('#vencimento').prop('disabled',true);
      $('#vencimento').val(null);
    });

var Today = new Date();

      $("#vencimento").datepicker({
        defaultDate:Today,
        showButtonPanel:true,
        todayHighlight: true,

      });
  });

  function hideSelected(value) {
  if (value && !value.selected) {
    return $('<span>' + value.text + '</span>');
  }
}

$(".add-more").click(function(){   
          var html = $(".copy").html(); 
          
          $(".after-add-more").after(html);
          $('#vinculo_copy').select2({
              placeholder: "Selecione um serviço",
              allowClear: true,
              minimumResultsForSearch: -1,
              templateResult: hideSelected,
              });
          $('#vinculo_copy').val(null).change(); 
      });  
  
$("body").on("click",".remove",function(){   
    $(this).parents(".control-group").remove();  
    
});

</script>

<script>
  
 function removerVinculo(id,servico)
{

  var pendenciaID = id;
  var servicoID = servico;

  $.ajax({
            url: '{{url('admin/pendencia/removerVinculo')}}/'+pendenciaID+'/'+servicoID+'',
            method: 'GET',
            success: function(data) {

              $(this).data('status', data.completed);      
              
              console.log("Removeu Vinculo");
              },
            })
  
$('#vinculo').on('select2:select', function (e) {
    var data = e.params.data;
    console.log(data);
});
 
}
</script>


@stop