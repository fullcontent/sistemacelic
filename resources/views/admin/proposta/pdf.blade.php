<html>
    <head>
        <style>
            /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
            @page {
                margin: 0cm 0cm;
            }


            body {
                font-size: 12px;
                margin-top: 3cm;
                margin-left: 2cm;
                margin-right: 2cm;
                margin-bottom: 2cm;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 3cm;
             
                                
            }

            /** Define the footer rules **/
            footer {
                position: fixed; 
                bottom: 0cm; 
                left: 0cm; 
                right: 0cm;
                height: 2cm;
            }

            th, td {
                border-bottom: 1px solid #ddd;
                text-align: center;
            }
            
            table { page-break-after:auto }
tr    { page-break-inside:avoid; page-break-after:auto }
td    { page-break-inside:avoid; page-break-after:auto }
thead { display: table-row-group; }
tfoot { display:table-footer-group }

            table, th 
            {
                margin-top: 25px;
            }

            main {
                

                margin-top: 25px;
               

            }
            
            section
            {   
                
                z-index: -1000;
                position: fixed;
                bottom:0;
                top: 400px;
                right: 0;
               
            }


            
            
        </style>



    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
            <img src="http://sistemacelic.net/img/headerCastro.png" width="100%"/>
        </header>

        <section>
            <img src="http://sistemacelic.net/img/bgCastro.png" width="100%"/>
        </section>

        <footer>
            
            <img src="http://sistemacelic.net/img/footerCastro.png" width="100%"/>
        </footer>

        <!-- Wrap the content of your PDF inside a main tag -->
        
        <main style="page-break-after: never;">
        <div class="fixedBG"></div>   
        <div class="row" style="text-align:center;">
            <div class="col-xs-12">
                <h2 class="page-header text-center">

                    Proposta Comercial N°
                    {{$proposta->id}}
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


        <div class="row">

        <div class="col-xs-12 table-responsive servicos">
            <table class="table table-striped" id="servicos">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th width="75%">Serviço</th> 
                        
                        <th>Valor Unitário</th>
                        
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

                        @php
                            $subtotal = $subtotal + $s->valor;
                            @endphp

                        

                        @else

                            {{$index}}

                            @php
                            $subtotal = $s->valor;
                        @endphp

                        @endif

                        
                        
                    </td>
                    <td>
                        <p style="text-align:left;">
                            <b>{{$s->servico}}</b>
                            
                            
                        </p>
                        <p style="text-align:left;">{{$s->escopo}}</p>
                    </td>
                    
                    <td class="valor" id="{{$index}}">R$ {{number_format($s->valor,2)}}</td>
                    
                    </tr>
                
                @if(count($proposta->servicos->where('servicoPrincipal')) <= $key+1)
                    <tr>
                        <td></td>
                        <td style="font-weight:bold; text-align:right;">SubTotal</td>
                        <td style="font-weight:bold;"><span class="subTotal" id=' + i + '>R$ {{number_format($subtotal,2)}}</span></td>
                        
                    </tr>
                @endif   

                @endforeach


                        <tr>
                           <td></td>
                           <td style="font-weight:bold; text-align:right;"> Total: </td>
                           <td><span id="total" style="font-weight:bold;">R$ {{number_format($proposta->valorTotal(),2)}}</span></td>
                           
                        </tr>

                </tbody>
            </table>
        </div>

    </div>
        </main>

        <main style="page-break-before: always;">
        <div class="fixedBG"></div>  
            <div class="row documentos">

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

                <div class="row" style="text-align:center; margin-top:100px">
                    <div class="col-xs-12" style="overflow:hidden">

                        <p>Bal.
                            Camboriú/SC, {{ \Carbon\Carbon::parse(date('Y-m-d'))->isoFormat("D, MMMM, YYYY") }}</p>

                        <p class="text-center" style="margin-top: 50px;">
                            ________________________________________________________________</p>
                        <p class="text-center"><b>Castro Empresarial - Consultoria e Legalização Imobiliária</b></p>
                        <p class="text-center">CNPJ: 27.352.308/0001-52</p>
                    </div>
                </div>

        </main>
       


    </body>
</html>
