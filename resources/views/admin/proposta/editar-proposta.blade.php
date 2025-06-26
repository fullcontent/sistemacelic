@extends('adminlte::page')



@section('content_header')

@if($proposta->status == "Revisando")
    <h1>Revisar Proposta {{$proposta->id}}</h1>
@endif

@stop






@section('content')


<a href="{{route('propostaPDF', $proposta->id)}}" class="btn btn-info" target="_blank"> <i class="glyphicon glyphicon-file"></i>Gerar PDF</a>

@if($proposta->status != "Arquivada")
<button type="button" class="btn btn-default no-print" data-toggle="modal" data-target="#adicionar-servico">
<i class="fa fa-plus"></i> Adicionar Serviço
</button>
@endif

@if($proposta->status == "Aprovada")
@if(!count($proposta->servicosCriados))
        <a href="#" data-id="{{$proposta->id}}" class="btn btn-warning atualizar"><i class="glyphicon glyphicon-wrench"></i></a> 
@endif
@endif

<div class="pull-right no-print">
@if($proposta->status == 'Revisando')
							<a href="#" class="btn btn-default  status" data-id="{{$proposta->id}}">{{$proposta->status}}</a> 
							<a href="#" data-id="{{$proposta->id}}" class="btn btn-info  analisar"><i class="glyphicon glyphicon-send"></i></a>

						@elseif($proposta->status == 'Em análise')
							<a href="#" class="btn btn-info ">Em análise</a>

						@elseif($proposta->status == 'Aprovada')
							<a href="#" class="btn btn-success ">Aprovada</a>
						
						@elseif($proposta->status == 'Recusada')
							<a href="#" class="btn btn-danger ">Recusada</a>
                        @elseif($proposta->status == 'Arquivada')
							<a href="#" class="btn btn-default ">Arquivada</a>
						
						@endif

</div>


<div class="modal fade" id="adicionar-servico" style="display: none;">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Adicionar serviço</h4>
            </div>
            <div class="modal-body">
            {!! Form::open(['route'=>'proposta.store','id'=>'cadastroProposta','method'=>'post']) !!}



            {!! Form::hidden('proposta_id', $proposta->id, ['class'=>'form-control']) !!}

            <div class="form-group">
				{!! Form::label('servicos', 'Servicos:', array('class'=>'control-label')) !!}
				<br>
				{!! Form::select('servico_id', [], null, ['class'=>'form-control servicosLpu','style'=>'width:70%;']) !!}
				<button class="btn btn-success adicionar" type="button"><i class="glyphicon glyphicon-plus"></i> Adicionar</button>
                <button class="btn btn-info btn-xs adicionarSub" type="button"><i class="glyphicon glyphicon-plus"></i> SubServiço</button>   				
			</div>

            <div class="col-md-12 servicos" style="display:none">
            <table class="table table-striped table-bordered " id="datatable">
                <thead>
                <tr>
                        <th>#</th>
                        <th>Serviço</th> 
                        <th>Escopo</th>
                        <th width="20%">Responsável</th>
                        <th>Valor Unitário</th>
                        <th></th>  
                                            
                    </tr>
                </thead>
                   

                    <tbody>

                    </tbody>
            </table> 
		</div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
                {!! Form::close() !!}
            </div>
        </div>
        
    </div>

</div>


<div class="modal fade" id="editar-servico" style="display: none;">
    <div class="modal-dialog" style="width:80%;">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Editar serviço</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['route'=>'proposta.editarServico','id'=>'editarServicoProposta','method'=>'post']) !!}

                {!! Form::hidden('servico_id',null,['class'=>'form-control','id'=>'servico_id']) !!}

                {!! Form::label('servico', 'Servico:', array('class'=>'control-label')) !!}
                {!! Form::text('servico',null,['class'=>'form-control']) !!}

                {!! Form::label('escopo', 'escopo:', array('class'=>'control-label')) !!}
                {!! Form::text('escopo',null,['class'=>'form-control']) !!}

                {!! Form::label('valor', 'valor:', array('class'=>'control-label')) !!}
                {!! Form::text('valor',null,['class'=>'form-control']) !!}


                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
                {!! Form::close() !!}
            </div>
        </div>
        
    </div>

</div>


<section class="invoice">


<header>
      <div class="col-xs-12">
        <h2 class="text-center">
            
            <img src="http://sistemacelic.net/img/headerCastro.png" class="img-responsive">
            
          
        </h2>
      </div>
      <!-- /.col -->
</header>
    <!-- info row -->


    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header text-center">
                
            Proposta Comercial {{$proposta->id}}
            </h2>
        </div>

    </div>

    <div class="row invoice-info">
        <div class="col-xs-12">
            <p><b>Cliente: </b><span>{{$proposta->empresa->nomeFantasia}}</span></p>
            <p><b>CNPJ: </b><span>{{$proposta->unidade->cnpj}}</span></p>
            <p><b>Unidade: </b><span>{{$proposta->unidade->nomeFantasia}}</span></p>
            <p><b>Endereço: </b><span>{{$proposta->unidade->endereco}},{{$proposta->unidade->numero}} - {{$proposta->unidade->bairro}} {{$proposta->unidade->cidade}}/{{$proposta->unidade->uf}}</span></p>
        </div>

        

       

    </div>


    

    <div class="row">

        <div class="col-xs-12 table-responsive servicos">
            <table class="table table-striped" id="servicos">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th width="75%">Serviço</th> 
                        
                        <th>Valor Unitário</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @php $index=0 @endphp
                @foreach($proposta->servicos as $key => $s)
                    <tr id="{{$index}}">
                    <td>
                        @if(!$s->servicoPrincipal)
                         @php $index++ @endphp
                        @endif
                        
                        

                        @if($s->servicoPrincipal)
                
                        {{$index}}.{{$s->posicao}}

                        @else

                            {{$index}}
                        @endif

                        
                        
                    </td>
                    <td>
                        <p>
                            <b>{{$s->servico}}</b>
                            @if($s->servicoCriado)
                               <a href="{{route('servicos.show',$s->servicoCriado->id)}}" class="btn btn-xs btn-success no-print">{{$s->servicoCriado->os}}</a>
                            @endif
                        </p>
                        <p>{{$s->escopo}}</p>
                    </td>
                    
                    <td class="valor" id="{{$index}}">R$ {{number_format($s->valor,2)}}</td>
                    <td>
                        
                        <button class="btn btn-xs btn-danger remove no-print" type="button" data-id="{{$s->id}}" data-servicoID="{{$s->servicoCriado->id ?? ''}}" data-index="{{$index}}"><i class="glyphicon glyphicon-remove"></i></button>
                        <button class="btn btn-xs btn-info edit no-print" type="button" data-servico="{{$s->servico}}" data-escopo="{{$s->escopo}}" data-valor="{{$s->valor}}" data-id="{{$s->id}}" data-toggle="modal" data-target="#editar-servico"><i class="glyphicon glyphicon-edit"></i></button>

                    </td>
                    </tr>

                   
                    

                @endforeach
                <tr>
                           <td></td>
                           <td align="right" style="font-weight:bold;"> Total: </td>
                           <td><span id="total" style="font-weight:bold;"></span></td>
                           <td></td>
                        </tr>

                </tbody>
            </table>
        </div>

    </div>

    {!! Form::model($proposta,['route'=>['proposta.update', $proposta->id],'method'=>'put','enctype'=>'multipart/form-data','id'=>'editProposta']) !!}
    <div class="row documentos">

        <div class="col-xs-12">
            <p><b>Documentos a serem fornecidos: </b></p>
           
            {!! Form::textarea('documentos', $proposta->documentos, ['class'=>'form-control','id'=>'documentos']) !!}

            <p><b>Condições gerais:</b></p>
           
            {!! Form::textarea('condicoesGerais', $proposta->condicoesGerais, ['class'=>'form-control','id'=>'condicoesGerais']) !!}
            
            <p><b>Condições de pagamento:</b></p>
            
            {!! Form::textarea('condicoesPagamento', $proposta->condicoesPagamento, ['class'=>'form-control','id'=>'condicoesPagamento']) !!}

            <p><b>Dados para pagamento:</b></p>
            
            {!! Form::textarea('dadosPagamento', $proposta->dadosPagamento, ['class'=>'form-control','id'=>'dadosPagamento']) !!}
            
        </div>

        <div class="row">

            <div class="col-xs-12">
                <br>
                <p class="text-center">
                    <button type="" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button> 

                </p>
            </div>
        </div>

       {!! Form::close() !!}

   

    <footer>
    <div class="col-xs-12">
        <h2 class="text-center">
            <img src="http://sistemacelic.net/img/footerCastro.png" class="img-responsive"></h2>
        </div>
    </footer>


    <div class="row no-print">
        <div class="col-xs-12">
            
        </div>
    </div>
</section>



@endsection


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.tiny.cloud/1/mdbybl5sde5aiobm264wk5r9q3cua6n3r2z6hvc7aag1ry65/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>



<script>

$(function() {
    $('.atualizar').click(function(e) {        
		
		e.preventDefault();
		
		if(confirm("Gostaria de criar os serviços automaticamente?")){
        	var s = 1;
		}
		else{
			var s = 0;
		}

        var proposta_id = $(this).data('id');
		
         
		// console.log(status);

        $.ajax({
            type: "GET",
            url: "/admin/proposta/aprovar/"+proposta_id+"/"+s+"",
			datatType : 'JSON',
            data: {
				id: proposta_id,
				_token: CSRF_TOKEN,
				s: s,
				},
            success: function(data){
              
				console.log(data)	
                location.reload();
	  
			  
			  
            },
			error: function (result) {
                console.log(result)
            }
        });
    })
  })	
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

function calculaTotal(){
    
    var valorCalculado = 0;

    $(".valor").each(function () {
        valorCalculado += parseFloat($(this).text().replace(',', '').slice(3, 10));
    });

    $("#total").text(parseFloat(valorCalculado).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    }));

}


function calculaSubTotal(){
    
    var subTotal = 0;
    var sum=0;
    
    
    $( ".valor" ).each(function(i, value) {
       $('td[id^='+i+']').each(function(k, v){
        sum += parseFloat($(this).text().replace(',','').slice(3,10));

        if(k === $('td[id^='+i+']').length -1){
            var subtotal = parseFloat(sum).toLocaleString("pt-BR", { style: "currency" , currency:"BRL"});
            sum=0;
            
            
           
        }
        
        });
    });
}



$(function(){

var valorCalculado = 0;

$( ".valor" ).each(function() {
  valorCalculado += parseFloat($( this ).text().replace(',','').slice(3,10));
});

$("#total").text(parseFloat(valorCalculado).toLocaleString("pt-BR", { style: "currency" , currency:"BRL"}));


});


$(function () {

    var itens = [];
    var subTotal = 0;
    var index = "{{$index}}";
    var sum = 0;


    $(".valor").each(function (i, value) {

        itens.push(this.id);


        $('td[id^=' + i + ']').each(function (k, v) {
            sum += parseFloat($(this).text().replace(',', '').slice(3, 10));

            //    console.log($('td[id^='+i+']')) 

            if (k === $('td[id^=' + i + ']').length - 1) {
                var subtotal = parseFloat(sum).toLocaleString("pt-BR", {
                    style: "currency",
                    currency: "BRL"
                });
                sum = 0;

                var html = '<tr><td></td><td align="right" style="font-weight:bold;">SubTotal</td><td style="font-weight:bold;"><span class="subTotal" id=' + i + '>' + subtotal + '</span></td><td></td></tr>';

                $(this).parents('tr').after(html);




            }

        });
    });

})




//tinymce.init({
  //  selector: 'textarea',
    //menubar: false,
    //toolbar: false,
//});



var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(".remove").click(function (e) {
        
        e.preventDefault();


        var servico = $(this).data('id');
        var servicoID = $(this).data('servicoid');
        
        
        $(this).parents("tr").remove();
        
        if(servicoID)
        {
            if(confirm("Isso vai remover o servico que já foi criado"))
            {
                    $.ajax({
                type: "get",
                url: "/admin/proposta/removerServico/"+servico+"",
                data: {
                    id: servico, // < note use of 'this' here
                    _token: CSRF_TOKEN
                },
                success: function (data) {
                    
                    console.log("Removido")
                    calculaTotal();
                    calculaSubTotal();

                    
                },
                error: function (result) {
                    console.log("Serviço criado - erro ao remover");
                }
            });
            } 
            else{
                console.log("nao remover")
            }
        }
        else{
            $.ajax({
                type: "get",
                url: "/admin/proposta/removerServico/"+servico+"",
                data: {
                    id: servico, // < note use of 'this' here
                    _token: CSRF_TOKEN
                },
                success: function (data) {
                    
                    console.log("Removido")
                    calculaTotal();
                    calculaSubTotal();

                    
                },
                error: function (result) {
                    console.log("Serviço não criado - erro ao remover");
                }
            });
        }

})

$(".servicosLpu").select2({
	placeholder: 'Selecione o serviço',
  	allowClear: true,
	width: 'resolve', // need to override the changed default

    ajax: {
        url: "/api/servicosLpu/get",
        type: "get",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                _token: CSRF_TOKEN,
                search: params.term // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    }

});

var count = 0;


$(".adicionar").click(function (e) {
    e.preventDefault();



    var selected = $(".servicosLpu").val();

    if (selected) {
        var servicoLpu = $(".servicosLpu").val();
        var index = Math.floor(Math.random() * 100) + 1;


        count++;
        c = 0;


        $.ajax({
            type: "get",
            url: "/api/servicosLpu/find",
            data: {
                id: servicoLpu, // < note use of 'this' here
                _token: CSRF_TOKEN
            },
            success: function (data) {

                $.each(data, function (key, value) {
                    var html = '<tr id='+count+'>' +
                        '<td><span>' + count + '</span></td>' +
                        '<td><input type="hidden" name="servico[' + count + '][id]" value="' + value.id + '"></input><input type="text" class="form-control" name="servico[' + count + '][nome]" value="' + value.nome + '"></input></td>' +
                        '<td><textarea class="form-control" name="servico[' + count + '][escopo]" style="width:650px;height:90px">' + value.escopo + '</textarea></td>' +
                        '<td><select class="form-control responsavel" name="servico[' + count + '][responsavel_id]"></select></td>' +
                        '<td><input type="text" class="form-control" name="servico[' + count + '][valor]" value="' + value.valor + '"></input></td>' +
                        '<td><button class="btn btn-xs btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i></button></td>' +
                        '</tr>';

                    $('.servicos tbody').append(html);
                    $('.servicos').show();
                    $('.servicosLpu').val(null).change();

                        $(".responsavel").select2({
                        placeholder: 'Quem será responsável por esse serviço?',
                        allowClear: true,
                        ajax: {
                            url: "/api/responsaveis/get",
                            type: "get",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    _token: CSRF_TOKEN,
                                    search: params.term // search term
                                };
                            },
                            processResults: function (response) {
                                return {
                                    results: response
                                };
                                
                            },
                            cache: true
                        }
                    });



                })
            },
            error: function (result) {
                // alert('error');
            }
        });
    }
    else{
        console.log("Nenhum servico selecionado");
    }


})

$(".adicionarSub").click(function (e) {
    e.preventDefault();

    var last = $('.servicos tr:last').attr('id');

    var servicoP = last.substring(0,1);

           
    if($.trim(last).length > 1)
    {
        last = last.substring(0,1);
    }

    if(servicoP == last)
    {
        console.log("Diferente")
    }


    c++;

    

    var selected = $(".servicosLpu").val();

    if (selected) {
        var servicoLpu = $(".servicosLpu").val();
        var index = Math.floor(Math.random() * 100) + 1;


        


        $.ajax({
            type: "get",
            url: "/api/servicosLpu/find",
            data: {
                id: servicoLpu, // < note use of 'this' here
                _token: CSRF_TOKEN
            },
            success: function (data) {

                $.each(data, function (key, value) {
                    var html = '<tr id=' + last + '.' + c +'>' +
                        '<td><span>' + last + '.' + c +'</span></td>' +
                        '<td><input type="hidden" name="servico[' + last + '.' + c +'][id]" value="' + value.id + '"></input><input type="text" class="form-control" name="servico[' + last + '.' + c +'][nome]" value="' + value.nome + '"></input></td>' +
                        '<td><textarea class="form-control" name="servico[' + last + '.' + c +'][escopo]" style="width:6    50px;height:90px">' + value.escopo + '</textarea></td>' +
                        '<td><select class="form-control responsavel" name="servico[' + last + '.' + c +'][responsavel_id]"></select></td>' +
                        '<td><input type="text" class="form-control" name="servico[' + last + '.' + c +'][valor]" value="' + value.valor + '"></input></td>' +
                        '<td><button class="btn btn-xs btn-danger removeSub" type="button"><i class="glyphicon glyphicon-remove"></i></button></td>' +
                        '</tr>';

                    $('.servicos tbody').append(html);
                    $('.servicos').show();
                    $('.servicosLpu').val(null).change();
                    $(".responsavel").select2({
                        placeholder: 'Quem será responsável por esse serviço?',
                        allowClear: true,
                        ajax: {
                            url: "/api/responsaveis/get",
                            type: "get",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    _token: CSRF_TOKEN,
                                    search: params.term // search term
                                };
                            },
                            processResults: function (response) {
                                return {
                                    results: response
                                };
                                
                            },
                            cache: true
                        }
                    });


                })
            },
            error: function (result) {
                // alert('error');
            }
        });
    }
    else{
        console.log("Nenhum servico selecionado");
    }


})


$('.edit').on('click', function (event) {



    var servico = $(this).data("servico")
    var escopo = $(this).data("escopo")
    var valor = $(this).data("valor")
    var id = $(this).data("id")

    console.log(id)


  var modal = $('#editar-servico')

  modal.find('.modal-body #servico_id').val(id)

  modal.find('.modal-body input#servico').val(servico)
  modal.find('.modal-body input#escopo').val(escopo)
  modal.find('.modal-body input#valor').val(valor)


  $("#editar-servico").submit(function(e){
       

        $.ajax({
        url: '{{url("admin/proposta/editarServico")}}',
        data: {
                    id: id,
                    servico: servico,
                    valor: valor,
                    escopo: escopo,
                    _token: CSRF_TOKEN
                },
        type: "GET",
               
        success: function(data){
            
           

        }, 
        error: function(){
              alert(" Erro ao editar serviço ");
              
         }

        }); 

        });
  })

  




</script>
@endsection

@section('css')
<style>
    .table>tbody>tr>td{
        vertical-align:middle;
    }

    header{
        display:none;
    }
    footer{
        display:none;
    }
    

    @page {
    size: A4;
    margin: 11mm 17mm 17mm 17mm;
    }

    .pagenum:after {
    content: counter(page);
    }
       
    @media print {
        
        body{
            font-family: "Calibri";
            
        }


        header{
            display: block;
            
        }
    
        footer{
            display: inline;
            position: fixed;
            bottom: 0;
            margin: 0;
            padding:0;

        }

        .content.servicos{
            page-break-inside: avoid;

        }

        .documentos{
            page-break-before: always;
        }

    
    
    }

</style>
@endsection