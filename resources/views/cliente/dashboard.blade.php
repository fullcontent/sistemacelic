@extends('adminlte::page')




@section('content_header')
    <h1 style="font-weight: 700; color: #2c3e50; font-size: 24px; margin-bottom: 5px;">Dashboard do Cliente</h1>
@stop

@section('content')

@php
    $user = Auth::user();
    // NOTE: servicos.solicitante stores a Solicitante ID (integer), not user->name.
    // "Meus em andamento" shows all in-progress services scoped to the client's companies
    // (the same scope used by listaMeusAndamento in ClienteController).
    $meusAndamentoCount = $servicos ? $servicos->where('situacao', 'andamento')->count() : 0;
    $todosAndamentoCount = $servicos ? $servicos->where('situacao', 'andamento')->count() : 0;
    $finalizadosCount = $servicos ? $servicos->where('situacao', 'finalizado')->count() : 0;
    $standByCount = $servicos ? $servicos->where('situacao', 'standBy')->count() : 0;
    $pendenciasCount = $pendencias ? $pendencias->where('status', 'pendente')->count() : 0;
@endphp

  <div class="row" style="margin-bottom: 20px;">
    <!-- Card 1.1: Serviços em andamento -->
    <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
      <div class="dash-card" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; border-radius: 12px; padding: 18px; position: relative; box-shadow: 0 4px 12px rgba(14,165,233,0.25);">
        <div style="font-size: 28px; font-weight: 700; line-height: 1;">{{ $todosAndamentoCount }}</div>
        <div style="font-size: 12px; font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">Em andamento</div>
        <div style="margin-top: 14px;">
          <a href="{{ route('cliente.servico.andamento') }}" class="btn-pill-card" style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; border-radius: 50px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-decoration: none; backdrop-filter: blur(4px);">
            Mais informações <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 1.3: Serviços finalizados -->
    <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
      <div class="dash-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border-radius: 12px; padding: 18px; position: relative; box-shadow: 0 4px 12px rgba(16,185,129,0.25);">
        <div style="font-size: 28px; font-weight: 700; line-height: 1;">{{ $finalizadosCount }}</div>
        <div style="font-size: 12px; font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">Serviços finalizados</div>
        <div style="margin-top: 14px;">
          <a href="{{ route('cliente.servico.finalizado') }}" class="btn-pill-card" style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; border-radius: 50px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-decoration: none; backdrop-filter: blur(4px);">
            Mais informações <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 1.4: Serviços em stand-by -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
      <div class="dash-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; border-radius: 12px; padding: 18px; position: relative; box-shadow: 0 4px 12px rgba(245,158,11,0.25);">
        <div style="font-size: 28px; font-weight: 700; line-height: 1;">{{ $standByCount }}</div>
        <div style="font-size: 12px; font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">Serviços em stand-by</div>
        <div style="margin-top: 14px;">
          <a href="{{ route('cliente.servico.standBy') }}" class="btn-pill-card" style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; border-radius: 50px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-decoration: none; backdrop-filter: blur(4px);">
            Mais informações <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Card 1.5: Minhas pendências em aberto -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
      <div class="dash-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; border-radius: 12px; padding: 18px; position: relative; box-shadow: 0 4px 12px rgba(239,68,68,0.25);">
        <div style="font-size: 28px; font-weight: 700; line-height: 1;">{{ $pendenciasCount }}</div>
        <div style="font-size: 12px; font-weight: 600; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.95;">Minhas pendências em aberto</div>
        <div style="margin-top: 14px;">
          <a href="{{ route('cliente.pendencias.lista') }}" class="btn-pill-card" style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; border-radius: 50px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-decoration: none; backdrop-filter: blur(4px);">
            Mais informações <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>
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
  .content-header .breadcrumb { display: none !important; }
  #map {
    border-radius: 8px;
  }
  .leaflet-svg-icon {
    background: transparent;
    border: none;
  }
  .dash-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .dash-card:hover {
    transform: translateY(-3px);
  }
  .btn-pill-card {
    transition: all 0.2s ease;
  }
  .btn-pill-card:hover {
    background: rgba(255,255,255,0.35) !important;
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

  var unidades = {!! json_encode($unidades->map(function($u) {
      return [
          'id'           => $u->id,
          'nomeFantasia' => $u->nomeFantasia,
          'cidade'       => $u->cidade,
          'uf'           => $u->uf,
          'latitude'     => $u->latitude,
          'longitude'    => $u->longitude,
          'licenca_status' => $u->licenca_status ?? 'vencida',
      ];
  })) !!};

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
        if (!isNaN(lat) && !isNaN(lng) && lat >= -33.75 && lat <= 5.27 && lng >= -73.99 && lng <= -34.79) {
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