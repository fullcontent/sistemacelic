@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de serviços</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px; overflow:scroll">
				<div class="box-header">
					
				</div>
				<table id="example" class="display" style="width:100%">
					<thead>
						<tr>
                            <th>ServicoID</th>
							<th>Razão Social</th>
							<th>Código</th>
							<th>Nome</th>
							<th>CNPJ</th>
							<th>Status</th>
							<th>Imóvel</th>
							<th>Ins. Estadual</th>
							<th>Ins. Municipal</th>
							<th>Ins. Imob.</th>
							<th>RIP</th>
							<th>Matrícula RI</th>
							<th>Área da Loja</th>
							<th>Endereço</th>
							<th>Número</th>
							<th>Complemento</th>
							<th>Data Inauguração</th>
							<th>Cidade</th>
							<th>UF</th>
							<th>CEP</th>
							<th>Tipo</th>
							<th>O.S.</th>
							<th>Situação</th>
							<th>Responsável</th>
							<th>Co-Responsável</th>
							<th>Nome</th>
							<th>Solicitante</th>
							<th>Departamento</th>
							<th>N° Protocolo</th>
							<th>Emissão Protocolo</th>
							<th>Tipo Licença</th>
							<th>Proposta</th>
							<th>Emissão Licença</th>
							<th>Validade Licença</th>
							<th>Valor Total</th>
							<th>Valor em Aberto</th>
							<th>Finalizado</th>
							<th>Criação</th>
                            
						  </tr>
					</thead>
				  </table>
				  
			</div>
	 		

@endsection


@section('css')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jqc-1.12.4/dt-1.13.2/b-2.3.4/sl-1.6.0/datatables.min.css"/>

<style>
	div.dt-button-collection button.dt-button.active:not(.disabled){
		background: none;
		background-color: aquamarine;
	}

	div.dt-button-collection button.dt-button{
		background: none;
		background-color:brown;
	}
		
</style>


@endsection


@section('js')



<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.2/af-2.5.2/b-2.3.4/b-colvis-2.3.4/b-html5-2.3.4/b-print-2.3.4/cr-1.6.1/r-2.4.0/sb-1.4.0/sp-2.1.1/datatables.min.js"></script>
<script>
$(document).ready(function () {
   let servicos =  $('#example').DataTable({
		"autoWidth": true,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
			   },
        dom: "Bfrtip",
        colReorder: true,
		
        buttons: [{
                extend: "excelHtml5",
                filename: 'Relatorio_Servicos_Celic_'+'<?php echo date('d-m-Y'); ?>',
				autoFilter: true,
                exportOptions: {
                    orthogonal: "exportxls",
                    columns: ':visible',
					
                    modifier: {
                        order: 'index'
                    }
                }
            },
            {
                extend: "csvHtml5",
				filename: 'Relatorio_Servicos_Celic_'+'<?php echo date('d-m-Y'); ?>',
                exportOptions: {
                    orthogonal: "exportcsv",
                    columns: ':visible',
					
                    modifier: {
                        order: 'index'
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: function(e){
					return e.value("A4");
				},
				filename: 'Relatorio_Servicos_Celic_'+'<?php echo date('d-m-Y'); ?>',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        order: 'index'
                    }
                }
            },
			{
                extend: 'print',
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                         
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                },
				exportOptions: {
                    columns: ':visible',
                    modifier: {
                        order: 'index'
                    }
                }
            },

            {
                extend: 'collection',
                text: 'Editar colunas',
                buttons: ['columnsVisibility'],
                visibility: true
            },
            {
                extend: 'searchBuilder',
				text: 'Filtros',
                config: {
                    depthLimit: 1	
                },
				searchBuilder: {
        preDefined: {
            criteria: [
                {
                    
                    condition: '='                    
                }
            ],
            logic: 'OR' // Use `OR` logic for the group
        }
    },

            },
        ],
        deferRender: true,
        scroller: true,
        "processing": false,
        "serverSide": false,
        ajax: {
            "url": "{{ route('getAllServicesJSON') }}",
            "type": "GET",
            "dataSrc": "",
            
                      
        },
    });
    $('#example').on('click', 'tbody tr', function() {

 
        var id = servicos.row(this).data()[0];



        window.location.href = "servicos/" + id
});

        $('tr').css('cursor','pointer');
});


</script>
  @stop