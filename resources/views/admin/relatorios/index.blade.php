@extends('adminlte::page')
@section('content_header')
    <h1>Relatórios</h1>
@stop


@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<div class="col-md-3">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Completos</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">


            <a href="{{route('relatorio.completo')}}" class="btn btn-block btn-default btn-lg">Serviços</a>
                

            <a href="{{route('relatorio.taxas')}}" target="_blank" class="btn btn-block btn-default btn-lg">Taxas</a>

            <a href="{{route('relatorio.pendencias')}}" target="_blank"
                class="btn btn-block btn-default btn-lg">Pendências</a>

               <a href="{{route('relatorio.arquivos')}}" target="_blank"
   class="btn btn-block btn-default btn-lg">
   <i class="fa fa-folder-open"></i> Arquivos
</a>
<a href="{{route('relatorioEmpresasCSV')}}" target="_blank"
   class="btn btn-block btn-default btn-lg">
   <i class="fa fa-building"></i> Empresas
</a>
<a href="{{route('relatorioPropostasCSV')}}" target="_blank"
   class="btn btn-block btn-default btn-lg">
   <i class="fa fa-file-signature"></i> Propostas
</a>
<a href="{{route('relatorioFaturamentosCSV')}}" target="_blank"
   class="btn btn-block btn-default btn-lg">
   <i class="fa fa-dollar-sign"></i> Faturamentos
</a>

<a href="{{route('relatorioReembolsosCSV')}}" target="_blank"
   class="btn btn-block btn-default btn-lg">
   <i class="fa fa-money-bill-wave-alt"></i> Reembolsos
</a>

        </div>

    </div>

</div>

<div class="col-md-3">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Pendências</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">
           

            {!! Form::open(['route'=>'relatorioPendenciasFilter','method'=>"post"]) !!}


            <div class="col-md-12">
                {!! Form::label('empresa_id', 'Empresas:', array('class'=>'control-label')) !!}
                
                {{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas','style'=>'width:100%']) }}
                <div style="margin-top:5px;">
                    <a href="#" id="selectAll" class="btn btn-xs btn-default">Todas</a> 
                    <a href="#" id="selectNone" class="btn btn-xs btn-default">Limpar</a>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group" style="margin-top:10px;">
                {!! Form::label('status', 'Status', array('class'=>'control-label')) !!}
                {!! Form::select('status', array('pendente' => 'Pendente', 'concluido' => 'Concluido'), null, ['class'=>'form-control','id'=>'status']) !!}
                </div>
            </div>


          



        </div>

        <div class="box-footer">
                
            <button type="submit" class="btn btn-info" id="gerarRelatorio">Gerar</button>
</div>





{!! Form::close() !!}

    </div>

</div>


<div class="col-md-3">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Serviços</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">
           

            {!! Form::open(['route'=>'relatorioServicosFilter','method'=>"post"]) !!}


            <div class="col-md-12">
                {!! Form::label('empresa_id', 'Empresas:', array('class'=>'control-label')) !!}
                
                {{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas2','style'=>'width:100%']) }}
                <div style="margin-top:5px;">
                    <a href="#" id="selectAll2" class="btn btn-xs btn-default">Todas</a> 
                    <a href="#" id="selectNone2" class="btn btn-xs btn-default">Limpar</a>
                </div>
            </div>

            


          



        </div>

        <div class="box-footer">
                
            <button type="submit" class="btn btn-info" id="gerarRelatorio">Gerar</button>
</div>





{!! Form::close() !!}




    </div>

</div>

<div class="col-md-12">

<div class="box box-info">
<div class="box-header with-border">

<h3 class="box-title"><i class="fa fa-history"></i> Últimos 25 relatórios gerados</h3>
</div>
<table id="reports-table" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Nome do Arquivo</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <!-- Os dados da tabela serão preenchidos pelo JavaScript -->
                </tbody>
            </table>
</div>
                
               
            
        </div>

@endsection


@section('js')
<script>
$(document).ready(function () {
    function fetchReports() {
        $.ajax({
            url: 'listar-relatorios', // URL da rota que lista os relatórios
            method: 'GET',
            success: function (data) {
                const tableBody = $('#reports-table tbody');
                tableBody.empty(); // Limpa o conteúdo anterior

                data.forEach(function (report) {
                    const date = new Date(report.date * 1000).toLocaleString('pt-BR'); // Formata a data
                    const row = `
                        <tr>
                            <td>${report.name}</td>
                            <td>${date}</td>
                            <td>
                                <a href="${report.download_link}" class="btn btn-success" download>Download</a>
                               
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            },
            error: function (error) {
                console.error("Erro ao buscar relatórios:", error);
            }
        });
    }

    // Chama a função para carregar os relatórios ao iniciar
    fetchReports();

    // Evento de clique no botão de exclusão
    $(document).on('click', '.delete-report', function () {
        const filename = $(this).data('filename');

        if (confirm('Tem certeza que deseja excluir este relatório?')) {
            $.ajax({
                url: `/deleteRelatorio/${filename}`, // URL para deletar o relatório
                type: 'DELETE',
                success: function (response) {
                    alert(response.message);
                    fetchReports(); // Recarrega os relatórios após a exclusão
                },
                error: function (xhr) {
                    alert(xhr.responseJSON.message || 'Erro ao excluir o relatório.');
                    console.error("Erro ao excluir o relatório:", error); // Adicionado para depuração

                }
            });
        }
    });
});
</script>


<script>
    $(document).ready(function() {

$("#empresas").select2({
	placeholder: 'Selecione a empresa',
	allowClear: true,
	multiple: true,
});

$("#empresas").val('').trigger('change');

$("#empresas2").select2({
	placeholder: 'Selecione a empresa',
	allowClear: true,
	multiple: true,
});

$("#empresas2").val('').trigger('change');

$("#selectAll").click(function(){ 
		
		$("#empresas option").each(function(){
			$(this).prop('selected', true);
		});

        console.log("Todas selecionadas");


		
	});

    $("#selectAll2").click(function(){ 
		
		$("#empresas2 option").each(function(){
			$(this).prop('selected', true);
		});

        console.log("Todas selecionadas");


		
	});

	$("#selectNone").click(function(){ 
		
		$('#empresas').val(null).trigger('change');


        console.log("Limpar selecao");
		
	});

    $("#selectNone2").click(function(){ 
		
		$('#empresas2').val(null).trigger('change');


        console.log("Limpar selecao");
		
	});






  

});
</script>



@endsection 