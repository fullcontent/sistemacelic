@component('mail::message')
# Vencimento de licença em breve


<h2><b>Unidade: </b>{{$servico->unidade->nomeFantasia}}</h2>
<h2><b>Serviço: </b>{{$servico->nome}}</h2>


A licença vencerá em 60 dias.


@component('mail::button', ['url' => route('servicos.show',$servico->id),'color'=>'red'])
Iniciar renovação
@endcomponent

Salve,<br>
Castro Empresarial
@endcomponent
