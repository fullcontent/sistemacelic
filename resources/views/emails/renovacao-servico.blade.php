@component('mail::message')
# Notificação de Renovação de Serviço

O serviço abaixo está se aproximando do prazo de renovação configurado.

<h2><b>OS:</b> {{$servico->os}}</h2>
<h2><b>Serviço:</b> {{$servico->nome}}</h2>
@if($servico->unidade)
<h2><b>Unidade:</b> {{$servico->unidade->nomeFantasia}}</h2>
@endif
@if($servico->empresa)
<h2><b>Empresa:</b> {{$servico->empresa->razaoSocial}}</h2>
@endif
<h2><b>Data de Vencimento:</b> {{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</h2>

@component('mail::button', ['url' => route('servicos.show', $servico->id), 'color' => 'blue'])
Visualizar Serviço
@endcomponent

Salve,<br>
Castro Licenciamentos - Consultoria e Legalização Imobiliária
@endcomponent
