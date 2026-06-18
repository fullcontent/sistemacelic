@extends('adminlte::page')




@section('content')




  <div class="row">

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{count($servicos->where('situacao', 'andamento'))}}</h3>

          <p>Serviços em andamento</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a href="{{route('cliente.servico.andamento')}}" class="small-box-footer">Mais informações <i
            class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{count($servicos->where('situacao', 'finalizado'))}}</h3>

          <p>Serviços finalizados</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a href="{{route('cliente.servico.finalizado')}}" class="small-box-footer">Mais informações <i
            class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    <!-- ./col -->

  </div>

  <!-- Tabs structure (Licenças and Projetos) and Map -->
  <div class="row">
    <!-- Left Side: Tabs for Licenças and Projetos -->
    <div class="col-md-6">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_licencas" data-toggle="tab">Licenças de Operação</a></li>
          <li><a href="#tab_projetos" data-toggle="tab">Projetos/Não Renováveis</a></li>
        </ul>
        <div class="tab-content">
          <!-- Tab 1: Licenças de Operação -->
          <div class="tab-pane active" id="tab_licencas">
            <div class="table-responsive">
              <table id="licencaOperacao" class="table table-bordered table-hover" style="width:100%;">
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Unidade</th>
                    <th>Serviço</th>
                    <th>Vencimento</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($servicos->where('tipo', '=', 'licencaOperacao')->where('situacao', '=', 'andamento') as $servico)
                    <tr>
                      <td>{{$servico->unidade->codigo}}</td>
                      <td><a href="{{route('cliente.unidade.show', $servico->unidade->id)}}">{{$servico->unidade->nomeFantasia}}</a></td>
                      <td><a href="{{route('cliente.servico.show', $servico->id)}}">{{$servico->nome}}</a></td>
                      <td>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <!-- Tab 2: Projetos/Não Renováveis -->
          <div class="tab-pane" id="tab_projetos">
            <div class="table-responsive">
              <table id="nRenovaveis" class="table table-bordered table-hover" style="width:100%;">
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Unidade</th>
                    <th>O.S.</th>
                    <th>Nome</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($servicos->where('tipo', 'nRenovaveis')->where('situacao', 'andamento') as $servico)
                    <tr>
                      <td>{{$servico->unidade->codigo}}</td>
                      <td><a href="{{route('cliente.unidade.show', $servico->unidade->id)}}">{{$servico->unidade->nomeFantasia}}</a></td>
                      <td>{{$servico->os}}</td>
                      <td><a href="{{route('cliente.servico.show', $servico->id)}}">{{$servico->nome}}</a></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- /.tab-content -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>

    <!-- Right Side: Leaflet Map -->
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-map-marker text-primary"></i> Localização das Unidades</h3>
        </div>
        <div class="box-body no-padding">
          <div id="map" style="height: 380px; width: 100%;"></div>
        </div>
      </div>
    </div>
  </div>



@endsection



@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
  #map {
    border-radius: 3px;
  }
  .leaflet-svg-icon {
    background: transparent;
    border: none;
  }
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
  $(document).ready(function () {
    // Initialize DataTables
    var tableLicencas = $('#licencaOperacao').DataTable({
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

    var tableProjetos = $('#nRenovaveis').DataTable({
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

    // Fix columns alignment when shifting tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    // Initialize Leaflet Map
    initLeafletMap();
  });

  var unidades = {!! json_encode($unidades) !!};

  function getMarkerIcon(status) {
    var color = '#dd4b39'; // Red (not vigente by default)
    if (status === 'vigente') {
      color = '#00a65a'; // Green (vigente)
    }
    
    var svg = `<svg viewBox="0 0 24 24" width="32" height="32" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="${color}" stroke="#ffffff" stroke-width="1.2"/>
    </svg>`;
    
    return L.divIcon({
      html: svg,
      className: 'leaflet-svg-icon',
      iconSize: [32, 32],
      iconAnchor: [16, 32],
      popupAnchor: [0, -32]
    });
  }

  function initLeafletMap() {
    var map = L.map('map').setView([-15.7801, -47.9292], 4);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var markers = [];

    $.each(unidades, function (index, value) {
      if (value.latitude && value.longitude) {
        var lat = parseFloat(value.latitude);
        var lng = parseFloat(value.longitude);
        if (!isNaN(lat) && !isNaN(lng)) {
          var icon = getMarkerIcon(value.licenca_status);
          var marker = L.marker([lat, lng], { icon: icon }).addTo(map);
          
          var routeUrl = "{{ route('cliente.unidade.show', ':id') }}".replace(':id', value.id);
          
          var popupContent = `
            <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 5px; min-width: 150px;">
              <h4 style="margin: 0 0 5px 0; font-weight: bold; font-size: 13px; color: #333;">${value.nomeFantasia}</h4>
              <p style="margin: 0 0 10px 0; font-size: 11px; color: #777;">${value.cidade} - ${value.uf}</p>
              <a href="${routeUrl}" class="btn btn-xs btn-primary" style="color: white; font-weight: 500;">Ver Unidade</a>
            </div>
          `;
          
          marker.bindPopup(popupContent);
          
          // Hover tooltip showing name
          marker.bindTooltip(value.nomeFantasia, {
            permanent: false,
            direction: 'top'
          });
          
          markers.push(marker);
        }
      }
    });

    if (markers.length > 0) {
      var group = new L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    }
  }
</script>
@stop