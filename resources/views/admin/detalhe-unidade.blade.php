@extends('adminlte::page')
@section('content_header')
<h1>Detalhes da unidade</h1>
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
        
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="box box-gray collapsed-box">
          <div class="box-header with-border">
            <a href="#" data-widget="collapse"><h3 class="box-title">Cadastrar novo</h3></a>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          
          <!-- /.box-header -->
          <div class="box-body">
            <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'nRenovaveis'])}}" class="btn btn-block btn-default btn-flat">Projeto/Licença Não Renovável</a>
            <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'controleCertidoes'])}}" class="btn btn-block btn-default btn-flat">Controle de Certidões</a>
            <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'controleTaxas'])}}" class="btn btn-block btn-default btn-flat">Controle de Taxas</a>
            <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'facilitiesRealEstate'])}}" class="btn btn-block btn-default btn-flat">Facilities/Real Estate</a>
            <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'licencaOperacao'])}}" class="btn btn-block btn-default btn-flat">Licença de Operação</a>

          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <div class="col-md-9">
        
            @include('admin.components.widget-arquivos')
        
        
       
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','licencaOperacao')))
            @include('admin.components.widget-licencasOperacao')
        @else
        
       
      
        @endif
    
    </div>
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','nRenovaveis')))
            @include('admin.components.widget-naoRenovaveis')
            @else
           
        
        @endif
       
        
    </div>

    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','controleCertidoes')))
            @include('admin.components.widget-controleCertidoes')
        @else
            
        @endif
       
        
    </div>

    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','controleTaxas')))
            @include('admin.components.widget-controleTaxas')
            @else
           
        @endif
       
        
    </div>
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','facilitiesRealEstate')))
            @include('admin.components.widget-facilities')
        @else
       
        @endif
       
        
    </div>


    

     
</div>
@endsection


@section('js')


<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>

<script>
    
    
var licencaOperacao = $("#licencaOperacao").DataTable({
    "lengthChange":false,
    "ordering":false,
});

licencaOperacao.column(1).search('andamento','finalizado').draw();




var nRenovaveis = $("#nRenovaveis").DataTable({
    "lengthChange":false,
    "ordering":false,
});

nRenovaveis.column(1).search('andamento','finalizado').draw();



    $(function () {
        $('#lista-arquivos').DataTable({
          "paging": false,
          "lengthChange": false,
          "searching": true,
          "ordering": false,
          "info": false,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }           
  });
$('.confirmation').on('click', function () {
            return confirm('Você deseja excluir o serviço?');
          });
         
        });


$("#licencasOperacao_btn").on('click', function(e){
   
    e.preventDefault();

    licencaOperacao.state.clear();
            licencaOperacao.column(1).search('[arquivado]+[finalizado]+[andamento]+[nRenovado]').draw();
 
})

$("#nRenovaveis_btn").on('click', function(e){
   
   e.preventDefault();

   nRenovaveis.state.clear();
    nRenovaveis.column(1).search('[arquivado]+[finalizado]+[andamento]+[nRenovado]').draw();

})
    </script>
@stop