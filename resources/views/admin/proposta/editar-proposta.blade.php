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
                        <p><b>{{$s->servico}}</b> </p>
                        <p>{{$s->escopo}}</p>
                    </td>
                    
                    <td class="valor" id="{{$index}}">R$ {{number_format($s->valor,2)}}</td>
                    <td>
                        @if($proposta->status == 'Revisando')
                        <button class="btn btn-xs btn-danger remove no-print" type="button" data-id="{{$s->id}}" data-index="{{$index}}"><i class="glyphicon glyphicon-remove"></i></button>
                        @endif
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


$(function(){

    var itens = [];
    var subTotal = 0;
    var index = "{{$index}}";
    var sum=0;
    
    
    $( ".valor" ).each(function(i, value) {
    
       itens.push(this.id);
             

       $('td[id^='+i+']').each(function(k, v){
        sum += parseFloat($(this).text().replace(',','').slice(3,10));

        //    console.log($('td[id^='+i+']')) 

        if(k === $('td[id^='+i+']').length -1){
            var subtotal = parseFloat(sum).toLocaleString("pt-BR", { style: "currency" , currency:"BRL"});
            sum=0;

            var html = '<tr><td></td><td align="right" style="font-weight:bold;">SubTotal</td><td style="font-weight:bold;"><span class="subTotal" id='+i+'>'+subtotal+'</span></td><td></td></tr>';
            
             $(this).parents('tr').after(html);

             


             

            
             
        }
        
        });
    });

})


    



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
                calculaTotal();
                calculaSubTotal();

                
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