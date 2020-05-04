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
    @include('admin.components.widget-detalhes')
    @include('admin.components.widget-unidades')
    
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    
    @include('admin.components.widget-servicos')
    
  </div>
  <div class="col-md-6">
    
    @include('admin.components.widget-taxas')
    
  </div>

  <div class="col-md-6">
        
        @include('admin.components.widget-arquivos')
        
    </div>
</div>

@endsection

@section('js')
<script>

  

    $(function () {
        
        $('#servicos').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": false,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }


        });

        
  });


</script>
@endsection