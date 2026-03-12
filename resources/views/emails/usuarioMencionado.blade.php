@component('mail::message')
# Você foi mencionado no serviço abaixo.

<h3><b>Codigo: </b>{{$servico->unidade->codigo}}</h3>
<h3><b>Unidade: </b>{{$servico->unidade->nomeFantasia}}</h3>
<h3><b>Serviço: </b>{{$servico->nome}}</h3>@if(isset($resumo))
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #dd4b39; margin: 20px 0;">
        <i>{{ $resumo }}</i>
    </div>
@endif
<br>
@component('mail::button', ['url' => route($route, $servico->id), 'color' => 'red'])
Acesse para saber mais
@endcomponent


<br>
Castro Licenciamentos - Consultoria e Legalização Imobiliária
www.sistemacelic.com
www.castroli.com.br
@endcomponent