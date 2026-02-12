@component('mail::message')
# Você foi mencionado no serviço abaixo.

<h3><b>Codigo: </b>{{$servico->unidade->codigo}}</h3>
<h3><b>Unidade: </b>{{$servico->unidade->nomeFantasia}}</h3>
<h3><b>Serviço: </b>{{$servico->nome}}</h3>





@component('mail::button', ['url' => route($route, $servico->id), 'color' => 'red'])
Acesse para saber mais
@endcomponent


<br>
Castro Licenciamentos - Consultoria e Legalização Imobiliária
www.sistemacelic.com
www.castroli.com.br
@endcomponent