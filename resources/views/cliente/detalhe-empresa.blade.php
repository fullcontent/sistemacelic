@extends('adminlte::page')
@section('content_header')
<h1>Detalhes da empresa</h1>
@stop
@section('content')
@if (session()->has('success'))

<div class="alert alert-success alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
  {{ session('success') }}
</div>
@endif


<div class="row">
  <div class="col-md-12">
    
    @include('cliente.components.widget-detalhes')
    
  </div>
</div>

<!-- Barra de Busca Live para Serviços e Arquivos -->
<div class="row" style="margin-bottom: 20px;">
  <div class="col-md-12">
    <div class="input-group input-group-lg">
      <span class="input-group-addon"><i class="fa fa-search text-muted"></i></span>
      <input type="text" id="search-servicos" class="form-control" placeholder="Buscar por licenças, certificados, taxas, arquivos ou serviços... (ex: Bombeiros, Alvará, Vigilância)">
    </div>
  </div>
</div>

<!-- Licenças de Operação e Projetos/Licenças side-by-side with equal height -->
<div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 0;">
  @if(count($servicos->where('tipo','licencaOperacao')->where('situacao','<>','arquivado')))
  <div class="col-md-6" style="display: flex; flex-direction: column; margin-bottom: 20px;">
      @include('cliente.components.widget-licencasOperacao')
  </div>
  @endif
  @if(count($servicos->where('tipo','nRenovaveis')->where('situacao','<>','arquivado')))
  <div class="col-md-6" style="display: flex; flex-direction: column; margin-bottom: 20px;">
      @include('cliente.components.widget-nRenovaveis')
  </div>
  @endif
</div>


<!-- Other widgets (Certidões, Taxas, Facilities) -->
<div class="row">
  @if(count($servicos->where('tipo','controleCertidoes')->where('situacao','<>','arquivado')))
  <div class="col-md-6">
      @include('cliente.components.widget-controleCertidoes')
  </div>
  @endif

  @if(count($servicos->where('tipo','controleTaxas')->where('situacao','<>','arquivado')))
  <div class="col-md-6">
      @include('cliente.components.widget-controleTaxas')
  </div>
  @endif

  @if(count($servicos->where('tipo','facilitiesRealEstate')->where('situacao','<>','arquivado')))
  <div class="col-md-6">
      @include('cliente.components.widget-facilitiesRealEstate')
  </div>
  @endif
</div>

<!-- Serviços Arquivados -->
@if(count($servicos->where('situacao','arquivado')))
<div class="row">
  <div class="col-md-12" style="margin-top: 10px; margin-bottom: 20px;">
      @include('cliente.components.widget-arquivados')
  </div>
</div>
@endif
@endsection

@section('js')
<script>
$(document).ready(function() {
  var pageSize = 5;

  function initPagination(tableId) {
    var $table = $('#' + tableId);
    if ($table.length === 0) return;
    
    var $rows = $table.find('tbody tr');
    
    // Initially, all rows match search
    $rows.addClass('search-match').removeClass('search-mismatch');
    
    showPage(tableId, 1);
  }

  function showPage(tableId, page) {
    var $table = $('#' + tableId);
    if ($table.length === 0) return;
    
    var $box = $table.closest('.box');
    var $footer = $box.find('.box-footer');
    var $rows = $table.find('tbody tr.search-match');
    var totalItems = $rows.length;
    var totalPages = Math.ceil(totalItems / pageSize);
    
    // If no items or only 1 page, hide footer pagination but ensure matching rows are visible
    if (totalPages <= 1) {
      $footer.empty().hide();
      $rows.show();
      $table.find('tbody tr.search-mismatch').hide();
      return;
    }
    
    $footer.show();
    
    // Hide all mismatch rows
    $table.find('tbody tr.search-mismatch').hide();
    
    // Show only rows of the current page, hide other match rows
    var start = (page - 1) * pageSize;
    var end = start + pageSize;
    
    $rows.each(function(index) {
      if (index >= start && index < end) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
    
    // Render pagination controls
    $footer.empty();
    var $paginationContainer = $('<div class="pagination-container"></div>');
    var $ul = $('<ul class="pagination pagination-sm no-margin pull-right"></ul>');
    
    // Previous button
    var $prevLi = $('<li class="' + (page === 1 ? 'disabled' : '') + '"><a href="#">Anterior</a></li>');
    if (page > 1) {
      $prevLi.find('a').on('click', function(e) {
        e.preventDefault();
        showPage(tableId, page - 1);
      });
    } else {
      $prevLi.find('a').on('click', function(e) { e.preventDefault(); });
    }
    $ul.append($prevLi);
    
    // Page buttons
    for (var i = 1; i <= totalPages; i++) {
      (function(p) {
        var $li = $('<li class="' + (p === page ? 'active' : '') + '"><a href="#">' + p + '</a></li>');
        $li.find('a').on('click', function(e) {
          e.preventDefault();
          showPage(tableId, p);
        });
        $ul.append($li);
      })(i);
    }
    
    // Next button
    var $nextLi = $('<li class="' + (page === totalPages ? 'disabled' : '') + '"><a href="#">Próximo</a></li>');
    if (page < totalPages) {
      $nextLi.find('a').on('click', function(e) {
        e.preventDefault();
        showPage(tableId, page + 1);
      });
    } else {
      $nextLi.find('a').on('click', function(e) { e.preventDefault(); });
    }
    $ul.append($nextLi);
    
    $paginationContainer.append($ul);
    
    // Add text info on left side of footer
    var itemStart = start + 1;
    var itemEnd = Math.min(end, totalItems);
    var $info = $('<span class="text-muted pull-left" style="margin-top: 5px;">Mostrando ' + itemStart + ' a ' + itemEnd + ' de ' + totalItems + '</span>');
    $paginationContainer.append($info);
    
    $footer.append($paginationContainer);
  }

  // Initialize for target tables
  initPagination('licencaOperacao');
  initPagination('nRenovaveisTable');

  // Search logic
  $('#search-servicos').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    
    // 1. Process paginated tables
    ['licencaOperacao', 'nRenovaveisTable'].forEach(function(tableId) {
      var $table = $('#' + tableId);
      if ($table.length === 0) return;
      
      $table.find('tbody tr').each(function() {
        var $row = $(this);
        var text = $row.text().toLowerCase();
        if (text.indexOf(value) > -1) {
          $row.addClass('search-match').removeClass('search-mismatch');
        } else {
          $row.addClass('search-mismatch').removeClass('search-match');
        }
      });
      // Re-apply pagination to page 1
      showPage(tableId, 1);
    });

    // 2. Process non-paginated tables (other widgets)
    $('.box').each(function() {
      var $box = $(this);
      // Skip the widget-detalhes box (which doesn't contain data tables)
      if ($box.hasClass('box-gray')) {
        return;
      }
      
      // If this box contains a paginated table, skip normal filtering since showPage handled it
      var containsPaginated = $box.find('#licencaOperacao, #nRenovaveisTable').length > 0;
      if (containsPaginated) {
        // Just handle fade in/out of the box based on matches
        var visibleRows = $box.find('table tbody tr.search-match').length;
        if (visibleRows === 0 && value !== '') {
          $box.fadeOut(200);
        } else {
          $box.fadeIn(200);
        }
        return;
      }
      
      var hasTable = $box.find('table').length > 0;
      if (hasTable) {
        var visibleRows = 0;
        $box.find('table tbody tr').each(function() {
          var $row = $(this);
          var matches = $row.text().toLowerCase().indexOf(value) > -1;
          if (matches) {
            $row.show();
            visibleRows++;
          } else {
            $row.hide();
          }
        });

        if (visibleRows === 0 && value !== '') {
          $box.fadeOut(200);
        } else {
          $box.fadeIn(200);
        }
      }
    });
  });
});
</script>
@stop

