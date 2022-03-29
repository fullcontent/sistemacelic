@extends('adminlte::page')

@section('content_header')
    <h1>Gerar nova Proposta</h1>
@stop



@section('content')

<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">Proposta</h3>
    </div>
    @if($errors->any())
    {!! implode('', $errors->all('<div class="alert alert-danger alert-dismissible">:message</div>')) !!}
    @endif

    {!! Form::open(['route'=>'proposta.store','id'=>'cadastroProposta','method'=>'post']) !!}

    

    <div class="box-body">

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('unidade_id', 'Unidade:', array('class'=>'control-label')) !!}

                {!! Form::select('unidade_id', [], null, ['class'=>'form-control unidades']) !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">

                {!! Form::label('solicitante', 'Solicitante: ', array('class'=>'control-label')) !!}
                {!! Form::text('solicitante', null, ['class'=>'form-control','id'=>'solicitante']) !!}

            </div>

        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('responsavel_id', 'Responsável:', array('class'=>'control-label')) !!}
                {!! Form::select('responsavel_id', [], null, ['class'=>'form-control responsaveis']) !!}
            </div>


        </div>


		<div class="col-md-12">
			<div class="form-group">
				{!! Form::label('servicos', 'Servicos:', array('class'=>'control-label')) !!}
				<br>
				{!! Form::select('servico_id', [], null, ['class'=>'form-control servicosLpu','style'=>'width:70%;']) !!}
				<button class="btn btn-success adicionar" type="button"><i class="glyphicon glyphicon-plus"></i> Adicionar</button>
                <button class="btn btn-info btn-xs adicionarSub" type="button"><i class="glyphicon glyphicon-plus"></i> SubServiço</button>  
				
			</div>
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


        <div class="col-md-12">
            <div class="form-group">

                {!! Form::label('documentos', 'Documentos a serem fornecidos: ', array('class'=>'control-label')) !!}
                {!! Form::textarea('documentos', null, ['class'=>'form-control','id'=>'documentos']) !!}

            </div>

        </div>

        <div class="col-md-12">
            <div class="form-group">

                {!! Form::label('condicoesGerais', 'Condições Gerais: ', array('class'=>'control-label')) !!}
                {!! Form::textarea('condicoesGerais', null, ['class'=>'form-control','id'=>'condicoesGerais']) !!}

            </div>

        </div>

        <div class="col-md-12">
            <div class="form-group">

                {!! Form::label('condicoesPagamento', 'Condições de Pagamento:', array('class'=>'control-label')) !!}
                {!! Form::textarea('condicoesPagamento', null, ['class'=>'form-control','id'=>'condicoesPagamento']) !!}

            </div>

        </div>

        <div class="col-md-12">
            <div class="form-group">

                {!! Form::label('dadosPagamento', 'Dados para pagamento: ', array('class'=>'control-label')) !!}
                {!! Form::textarea('dadosPagamento', null, ['class'=>'form-control','id'=>'dadosPagamento']) !!}

            </div>

        </div>




</div>

    <div class="box-footer">
        <a href="javascript: history.go(-1)" class="btn btn-default">Voltar</a>
        <button type="submit" class="btn btn-info">Próximo Passo</button>
    </div>



    {!! Form::close() !!}





@endsection


@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script src="https://cdn.tiny.cloud/1/mdbybl5sde5aiobm264wk5r9q3cua6n3r2z6hvc7aag1ry65/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>


<script>




var documentos = "<ul><li>Projeto preventivo aprovado pelo CBMPE</li><li>Licença Ambiental</li></ul>";
var condicoesGerais = "<p>Todos os custos envolvidos no processo, tais como: taxas, emolumentos, entre outras, serão de responsabilidade do contratante.</p><p>Prazo para elaboração do protocolo: 20 dias</p><p>Prazo da proposta: 15 dias</p>";
var condicoesPagamento = "<p>100% na entrega dos documentos.</p>";
var dadosPagamento = "<p>Chave PIX: CNPJ 27.352.308/0001-52</p><p>Caixa Econômica Federal</p><p>Agencia: 0921 - Conta Corrente PJ: 6992-4</p><p>Castro Empresarial Serviços Administrativos LTDA-ME</p><p>CNPJ: 27.352.308/0001-52</p>";
var c = 0;

$('.servicos').hide();



$("body").on("click",".remove",function(){   
    $(this).parents("tr").remove(); 
    count--;

    if(count == 0)
    {
        $('.servicos').hide();
    }
});

$("body").on("click",".removeSub",function(){   
    
    $(this).parents("tr").remove(); 
    c--;
});






$("#documentos").html(documentos);
$("#condicoesGerais").html(condicoesGerais);
$("#condicoesPagamento").html(condicoesPagamento);
$("#dadosPagamento").html(dadosPagamento);


tinymce.init({
    selector: 'textarea',
    menubar: false,
    toolbar: false,
});

var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(".unidades").select2({
	placeholder: 'Selecione a unidade',
  	allowClear: true,

    ajax: {
        url: "/api/unidades/get",
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

$(".responsaveis").select2({
	placeholder: 'Quem será responsável pelos serviços?',
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

</script>
@endsection