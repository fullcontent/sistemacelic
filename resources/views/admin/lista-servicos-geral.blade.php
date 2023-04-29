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
                            <th>Licenciamento</th>
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





<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.13.2/af-2.5.2/b-2.3.4/b-colvis-2.3.4/b-html5-2.3.4/b-print-2.3.4/cr-1.6.1/r-2.4.0/sb-1.4.0/sp-2.1.1/datatables.min.js"></script>
<script>
$(document).ready(function () {

    let ptBR = {
    "emptyTable": "Nenhum registro encontrado",
    "info": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
    "infoFiltered": "(Filtrados de _MAX_ registros)",
    "infoThousands": ".",
    "loadingRecords": "Carregando...",
    "zeroRecords": "Nenhum registro encontrado",
    "search": "Pesquisar",
    "paginate": {
        "next": "Próximo",
        "previous": "Anterior",
        "first": "Primeiro",
        "last": "Último"
    },
    "aria": {
        "sortAscending": ": Ordenar colunas de forma ascendente",
        "sortDescending": ": Ordenar colunas de forma descendente"
    },
    "select": {
        "rows": {
            "_": "Selecionado %d linhas",
            "1": "Selecionado 1 linha"
        },
        "cells": {
            "1": "1 célula selecionada",
            "_": "%d células selecionadas"
        },
        "columns": {
            "1": "1 coluna selecionada",
            "_": "%d colunas selecionadas"
        }
    },
    "buttons": {
        "copySuccess": {
            "1": "Uma linha copiada com sucesso",
            "_": "%d linhas copiadas com sucesso"
        },
        "collection": "Coleção  <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
        "colvis": "Visibilidade da Coluna",
        "colvisRestore": "Restaurar Visibilidade",
        "copy": "Copiar",
        "copyKeys": "Pressione ctrl ou u2318 + C para copiar os dados da tabela para a área de transferência do sistema. Para cancelar, clique nesta mensagem ou pressione Esc..",
        "copyTitle": "Copiar para a Área de Transferência",
        "csv": "CSV",
        "excel": "Excel",
        "pageLength": {
            "-1": "Mostrar todos os registros",
            "_": "Mostrar %d registros"
        },
        "pdf": "PDF",
        "print": "Imprimir",
        "createState": "Criar estado",
        "removeAllStates": "Remover todos os estados",
        "removeState": "Remover",
        "renameState": "Renomear",
        "savedStates": "Estados salvos",
        "stateRestore": "Estado %d",
        "updateState": "Atualizar"
    },
    "autoFill": {
        "cancel": "Cancelar",
        "fill": "Preencher todas as células com",
        "fillHorizontal": "Preencher células horizontalmente",
        "fillVertical": "Preencher células verticalmente"
    },
    "lengthMenu": "Exibir _MENU_ resultados por página",
    "searchBuilder": {
        "add": "Adicionar Condição",
        "button": {
            "0": "Filtro",
            "_": "Filtro (%d)"
        },
        "clearAll": "Limpar Tudo",
        "condition": "Condição",
        "conditions": {
            "date": {
                "after": "Depois",
                "before": "Antes",
                "between": "Entre",
                "empty": "Vazio",
                "equals": "Igual",
                "not": "Não",
                "notBetween": "Não Entre",
                "notEmpty": "Não Vazio"
            },
            "number": {
                "between": "Entre",
                "empty": "Vazio",
                "equals": "Igual",
                "gt": "Maior Que",
                "gte": "Maior ou Igual a",
                "lt": "Menor Que",
                "lte": "Menor ou Igual a",
                "not": "Não",
                "notBetween": "Não Entre",
                "notEmpty": "Não Vazio"
            },
            "string": {
                "contains": "Contém",
                "empty": "Vazio",
                "endsWith": "Termina Com",
                "equals": "Igual",
                "not": "Não",
                "notEmpty": "Não Vazio",
                "startsWith": "Começa Com",
                "notContains": "Não contém",
                "notStartsWith": "Não começa com",
                "notEndsWith": "Não termina com"
            },
            "array": {
                "contains": "Contém",
                "empty": "Vazio",
                "equals": "Igual à",
                "not": "Não",
                "notEmpty": "Não vazio",
                "without": "Não possui"
            }
        },
        "data": "Data",
        "deleteTitle": "Excluir regra de filtragem",
        "logicAnd": "E",
        "logicOr": "Ou",
        "title": {
            "0": "Filtro",
            "_": "Filtro (%d)"
        },
        "value": "Valor",
        "leftTitle": "Critérios Externos",
        "rightTitle": "Critérios Internos"
    },
    "searchPanes": {
        "clearMessage": "Limpar Tudo",
        "collapse": {
            "0": "Painéis de Pesquisa",
            "_": "Painéis de Pesquisa (%d)"
        },
        "count": "{total}",
        "countFiltered": "{shown} ({total})",
        "emptyPanes": "Nenhum Painel de Pesquisa",
        "loadMessage": "Carregando Painéis de Pesquisa...",
        "title": "Filtros Ativos",
        "showMessage": "Mostrar todos",
        "collapseMessage": "Fechar todos"
    },
    "thousands": ".",
    "datetime": {
        "previous": "Anterior",
        "next": "Próximo",
        "hours": "Hora",
        "minutes": "Minuto",
        "seconds": "Segundo",
        "amPm": [
            "am",
            "pm"
        ],
        "unknown": "-",
        "months": {
            "0": "Janeiro",
            "1": "Fevereiro",
            "10": "Novembro",
            "11": "Dezembro",
            "2": "Março",
            "3": "Abril",
            "4": "Maio",
            "5": "Junho",
            "6": "Julho",
            "7": "Agosto",
            "8": "Setembro",
            "9": "Outubro"
        },
        "weekdays": [
            "Domingo",
            "Segunda-feira",
            "Terça-feira",
            "Quarta-feira",
            "Quinte-feira",
            "Sexta-feira",
            "Sábado"
        ]
    },
    "editor": {
        "close": "Fechar",
        "create": {
            "button": "Novo",
            "submit": "Criar",
            "title": "Criar novo registro"
        },
        "edit": {
            "button": "Editar",
            "submit": "Atualizar",
            "title": "Editar registro"
        },
        "error": {
            "system": "Ocorreu um erro no sistema (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Mais informações<\/a>)."
        },
        "multi": {
            "noMulti": "Essa entrada pode ser editada individualmente, mas não como parte do grupo",
            "restore": "Desfazer alterações",
            "title": "Multiplos valores",
            "info": "Os itens selecionados contêm valores diferentes para esta entrada. Para editar e definir todos os itens para esta entrada com o mesmo valor, clique ou toque aqui, caso contrário, eles manterão seus valores individuais."
        },
        "remove": {
            "button": "Remover",
            "confirm": {
                "_": "Tem certeza que quer deletar %d linhas?",
                "1": "Tem certeza que quer deletar 1 linha?"
            },
            "submit": "Remover",
            "title": "Remover registro"
        }
    },
    "decimal": ",",
    "stateRestore": {
        "creationModal": {
            "button": "Criar",
            "columns": {
                "search": "Busca de colunas",
                "visible": "Visibilidade da coluna"
            },
            "name": "Nome:",
            "order": "Ordernar",
            "paging": "Paginação",
            "scroller": "Posição da barra de rolagem",
            "search": "Busca",
            "searchBuilder": "Mecanismo de busca",
            "select": "Selecionar",
            "title": "Criar novo estado",
            "toggleLabel": "Inclui:"
        },
        "emptyStates": "Nenhum estado salvo",
        "removeConfirm": "Confirma remover %s?",
        "removeJoiner": "e",
        "removeSubmit": "Remover",
        "removeTitle": "Remover estado",
        "renameButton": "Renomear",
        "renameLabel": "Novo nome para %s:",
        "renameTitle": "Renomear estado",
        "duplicateError": "Já existe um estado com esse nome!",
        "emptyError": "Não pode ser vazio!",
        "removeError": "Falha ao remover estado!"
    },
    "infoEmpty": "Mostrando 0 até 0 de 0 registro(s)",
    "processing": "Carregando...",
    "searchPlaceholder": "Buscar registros"
}  


   let servicos =  $('#example').DataTable({
		"autoWidth": true,
		       "language": ptBR,
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