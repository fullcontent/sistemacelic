@extends('adminlte::page')

@section('title', 'Gerar nova Proposta')

@section('css')
<style>
	.form-container {
		background: #fff;
		border-radius: 8px;
		padding: 25px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
	}

	.btn-pill {
		border-radius: 50px;
		padding: 6px 20px;
		font-weight: 600;
		transition: all 0.2s;
	}

	.btn-pill:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
	}

	/* Scope controls inside form container */
	.form-container .form-control {
		border-radius: 6px;
		border: 1px solid #d2d6de;
		box-shadow: none;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
	}

	.form-container .form-control:focus {
		border-color: #3c8dbc;
		box-shadow: none;
	}

	.form-container label {
		font-weight: 600;
		color: #555;
		font-size: 0.95em;
		margin-bottom: 6px;
	}

	.form-container .box-body {
		padding: 0;
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Gerar Nova Proposta</h1>
	</div>
</div>
@stop

@section('content')
<div class="form-container">
    @if($errors->any())
    {!! implode('', $errors->all('<div class="alert alert-danger alert-dismissible" style="border-radius: 6px;">:message</div>')) !!}
    @endif

    {!! Form::open(['route'=>'proposta.store','id'=>'cadastroProposta','method'=>'post']) !!}

    <div class="box-body row">

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('unidade_id', 'Unidade:', array('class'=>'control-label')) !!}

                {!! Form::select('unidade_id', [], null, ['class'=>'form-control unidades']) !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">

                {!! Form::label('solicitante', 'Solicitante: ', array('class'=>'control-label')) !!}

               {!! Form::select('solicitante',$solicitantes,null, ['class'=>'form-control','id'=>'solicitante']) !!}

            </div>

        </div>

		<div class="col-md-12">
			<div class="form-group">
				{!! Form::label('servicos', 'Servicos:', array('class'=>'control-label')) !!}
				<br>
				{!! Form::select('servico_id', [], null, ['class'=>'form-control servicosLpu','style'=>'width:70%; display: inline-block; vertical-align: middle; margin-right: 10px;']) !!}
				<button class="btn btn-success adicionar btn-pill" type="button" style="padding: 6px 15px;"><i class="fa fa-plus"></i> Adicionar</button>
                <button class="btn btn-info btn-xs adicionarSub btn-pill" type="button" style="padding: 6px 15px; margin-left: 5px;"><i class="fa fa-plus"></i> SubServiço</button>  
			</div>
		</div>

		<div class="col-md-12 servicos" style="display:none; margin-top: 15px; margin-bottom: 15px;">
            <table class="table table-hover" id="datatable" style="width: 100%;">
                <thead>
                <tr style="background: #fcfcfc;">
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

	<div style="border-top: 1px solid #f4f4f4; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px;">
		<a href="javascript: history.go(-1)" class="btn btn-default btn-pill">Voltar</a>
		<button type="submit" class="btn btn-info btn-pill">Próximo Passo</button>
	</div>

    {!! Form::close() !!}
</div>
@endsection


@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>








<script>

$("#solicitante").select2({
            placeholder: 'Quem é o solicitante?',
            allowClear: true,
        });

        $("#solicitante").val('').trigger('change');



var documentos = "<ul><li>Projeto preventivo aprovado pelo CBMPE</li><li>Licença Ambiental</li></ul>";
var condicoesGerais = "<p>Todos os custos envolvidos no processo, tais como: taxas, emolumentos, entre outras, serão de responsabilidade do contratante.</p><p>Prazo para elaboração do protocolo: 20 dias</p><p>Prazo da proposta: 15 dias</p>";
var condicoesPagamento = "<p>Faturamento individual conforme finalização de cada item.</p>";
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