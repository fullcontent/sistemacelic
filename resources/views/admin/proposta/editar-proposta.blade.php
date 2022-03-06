@extends('adminlte::page')

@section('content_header')

@if($proposta->status == "Revisando")
    <h1>Revisar Proposta {{$proposta->id}}</h1>
@endif

@stop



@section('content')

<section class="invoice" style="">

    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header text-center">
                
            Proposta Comercial N°{{$proposta->id}}
            </h2>
        </div>

    </div>

    <div class="row invoice-info">
        <div class="col-xs-12">
            <p><b>Cliente: </b><span>{{$proposta->empresa->nomeFantasia}}</span></p>
            <p><b>CNPJ: </b><span>{{$proposta->empresa->cnpj}}</span></p>
            <p><b>Unidade: </b><span>{{$proposta->unidade->nomeFantasia}}</span></p>
            <p><b>Endereço: </b><span>{{$proposta->unidade->endereco}},{{$proposta->unidade->numero}} - {{$proposta->unidade->bairro}} {{$proposta->unidade->cidade}}/{{$proposta->unidade->uf}}</span></p>
        </div>

        

       

    </div>


    

    <div class="row">

        <div class="col-xs-12 table-responsive servicos">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th width="75%">Serviço</th> 
                        
                        <th>Valor Unitário</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($proposta->servicos as $key => $s)
                    <tr>
                    <td>
                        @if($s->servicoPrincipal)
                        {{$key}}.{{$s->posicao}}
                        @else
                        {{$key+1}}
                        @endif
                    </td>
                    <td>
                        <p><b>{{$s->servico}}</b> </p>
                        <p>{{$s->escopo}}</p>
                    </td>
                    
                    <td>R$ {{number_format($s->valor,2)}}</td>
                    <td>
                        @if($proposta->status == 'Revisando')
                        <button class="btn btn-xs btn-danger remove no-print" type="button" data-id="{{$s->id}}"><i class="glyphicon glyphicon-remove"></i></button>
                        @endif
                    </td>
                    </tr>
                    

                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    
    <div class="row">

        <div class="col-xs-12">
            <p><b>Documentos a serem fornecidos: </b></p>
            {!! $proposta->documentos !!}

            <p><b>Condições gerais:</b></p>
            {!! $proposta->condicoesGerais !!}
            
            <p><b>Condições de pagamento:</b></p>
            {!! $proposta->condicoesPagamento !!}

            <p><b>Dados para pagamento:</b></p>
            {!! $proposta->dadosPagamento !!}
            
        </div>

    <div class="row">
        <div class="col-xs-12" style="overflow:hidden">

            <p class="text-right" style="margin-bottom: 20px; padding-top:20px; margin-right:100px;">Bal. Camboriú/SC, {{ \Carbon\Carbon::parse(date('Y-m-d'))->isoFormat("D, MMMM, YYYY") }}</p>

            <p class="text-center" style="margin-top: 50px;">________________________________________________________________</p>
            <p class="text-center"><b>Castro Empresarial - Consultoria e Legalização Imobiliária</b></p>
            <p class="text-center">CNPJ: 27.352.308/0001-52</p>
        </div>
    </div>


    <div class="row no-print">
        <div class="col-xs-12">
            
        </div>
    </div>
</section>



@endsection


@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>



<script>

var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(".remove").click(function (e) {
    e.preventDefault();


        var servico = $(this).data('id');

        $(this).parents("tr").remove();
            


        $.ajax({
            type: "get",
            url: "/admin/proposta/removerServico/"+servico+"",
            data: {
                id: servico, // < note use of 'this' here
                _token: CSRF_TOKEN
            },
            success: function (data) {
                
                console.log("Removido")

                
            },
            error: function (result) {
                alert('Erro ao remover');
            }
        });
  
    


})


</script>
@endsection

@section('css')
<style>
    .table>tbody>tr>td{
        vertical-align:middle;
    }
</style>
@endsection