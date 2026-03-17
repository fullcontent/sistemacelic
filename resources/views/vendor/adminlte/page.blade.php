@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet"
          href="{{ asset('vendor/adminlte/dist/css/skins/skin-' . config('adminlte.skin', 'blue') . '.min.css')}} ">
    <link rel="stylesheet"
          href="{{ asset('vendor/mentions/jquery.mentions.css')}}">

    @stack('css')
    @yield('css')
    <style>
        .navbar-custom-menu {
            float: right !important;
        }
        .navbar-unit-name {
            float: left;
            display: flex;
            align-items: center;
            height: 50px;
            color: #333;
            font-weight: 600;
            white-space: nowrap;
            padding-left: 15px;
        }
        .navbar-unit-name a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s;
        }
        .navbar-unit-name a:hover {
            color: #007bff;
            text-decoration: underline;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "\f105";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            padding: 0 8px;
            color: #999;
        }
        .navbar-unit-name .breadcrumb {
            background: transparent;
            margin-bottom: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .navbar-unit-name .breadcrumb > li + li:before {
            padding: 0 8px;
            color: #ccc;
            content: "/\00a0";
        }
        @media (max-width: 991px) {
            .navbar-unit-name {
                display: none; /* Hide breadcrumb on smaller screens to avoid overlap */
            }
        }
        /* Garantir que o conteúdo não fique escondido sob o header fixo */
        body.fixed .content-wrapper {
            padding-top: 50px;
        }
    </style>
@stop

@section('body_class', 'skin-' . config('adminlte.skin', 'blue') . ' sidebar-mini ' . (config('adminlte.layout') ? [
    'boxed' => 'layout-boxed',
    'fixed' => 'fixed',
    'top-nav' => 'layout-top-nav'
][config('adminlte.layout')] : '') . (config('adminlte.collapse_sidebar') ? ' sidebar-collapse ' : ''))

@section('body')
    <div class="wrapper">

        <!-- Main Header -->
        <header class="main-header">
            @if(config('adminlte.layout') == 'top-nav')
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="{{ url(config('adminlte.dashboard_url', '#')) }}" class="navbar-brand">
                            {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
                        </a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            @each('adminlte::partials.menu-item-top-nav', $adminlte->menu(), 'item')
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
            @else
            <!-- Logo -->
            <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">{!! config('adminlte.logo_mini', '<b>A</b>LT') !!}</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">{!! config('adminlte.logo', '<b>Admin</b>LTE') !!}</span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle fa5" data-toggle="push-menu" role="button">
                    <span class="sr-only">{{ __('adminlte::adminlte.toggle_navigation') }}</span>
                </a>

                <!-- Breadcrumb: Onde estou -->
                <div class="navbar-unit-name">
                    @if (Route::currentRouteName() && Breadcrumbs::exists(Route::currentRouteName()))
                        {!! Breadcrumbs::render() !!}
                    @endif
                </div>

            @endif
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">



        <ul class="nav navbar-nav">

          <!-- CHANGELOG DO SISTEMA -->
          <li class="notifications-menu">
            <a href="#" id="changelogBtn" title="Novidades do Sistema">
              <i class="fa fa-history"></i>
            </a>
          </li>

                     <!-- MENÇOES DO USUARIO -->
        <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="glyphicon glyphicon-comment"></i>
              <span class="label label-info">{{count(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned'))}}</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">Você tem {{count(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned'))}} nova(s) menção(ões)</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  @foreach(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned') as $n)
                    <li>
                      <a href="{{$n->data['action']}}" data-notif-id="{{$n->id}}">
                        <i class="glyphicon glyphicon-comment text-info"></i> <small class="text-muted">({{ substr($n->id, 0, 8) }})</small> {{$n->data['mensagem']}} #{{$n->data['servico']}}
                      </a>
                    </li>
                   @endforeach
                </ul>
              </li>
              @if(count(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned')) > 0)
                <li class="footer"><a href="{{route('clearMentions')}}">Limpar notificações</a></li>
              @endif

              <li class="header" style="background-color: #f4f4f4;"><b>Histórico (Últimas 10)</b></li>
              <li>
                <ul class="menu">
                  @foreach(auth()->user()->notifications->where('type','App\Notifications\UserMentioned')->take(10) as $n)
                    <li style="{{ $n->read_at ? 'opacity: 0.6;' : '' }}">
                      <a href="{{$n->data['action']}}" data-notif-id="{{$n->id}}">
                        <i class="glyphicon glyphicon-comment {{ $n->read_at ? 'text-default' : 'text-info' }}"></i> 
                        <small class="text-muted">({{ substr($n->id, 0, 8) }})</small> {{$n->data['mensagem']}} #{{$n->data['servico']}}
                        @if($n->read_at)
                          <small class="pull-right" title="Visualizada"><i class="fa fa-check text-success"></i></small>
                        @endif
                      </a>
                    </li>
                  @endforeach
                </ul>
              </li>
            </ul>
          </li>
          
          
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="glyphicon glyphicon-bell"></i>
              <span class="label label-warning">{{count(auth()->user()->unreadNotifications->where('type','!=','App\Notifications\UserMentioned'))}}</span>
            </a>
                <ul class="dropdown-menu">
                       

              <li class="header">Você tem {{count(auth()->user()->unreadNotifications->where('type','!=','App\Notifications\UserMentioned'))}} nova(s) notificação(es)</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  
                  <!--
                        List notifications
                   -->
                
                   @foreach(auth()->user()->unreadNotifications->where('type','!=','App\Notifications\UserMentioned') as $n)
                        <li>
                    <a href="{{$n->data['action']}}" data-notif-id="{{$n->id}}">
                      <i class="glyphicon glyphicon-exclamation-sign text-yellow"></i> {{$n->data['mensagem']}}
                    </a></li>


                   @endforeach
                  
                </ul>
              </li>
              @if(auth()->user()->unreadNotifications->count())<li class="footer"><a href="{{route('clearNotifications')}}">Limpar notificações</a></li>@endif
              
            </ul>
          </li>
          

          <li class="dropdown tasks-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              
              <span class="hidden-xs">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="header"></li>
                <li>
                    <ul class="menu">
                    @if(auth()->user()->privileges == 'cliente')    
                    <li><a href="{{route('cliente.usuario.editar')}}">Editar perfil</a></li>
                    @elseif(auth()->user()->privileges == 'admin')
                    <li><a href="{{route('usuario.editar',auth()->user()->id)}}">Editar perfil</a></li>
                    @endif

                </ul>
            </li>
              <li class="footer"></li>
            </ul>
          </li>



                        
                        <li>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-fw fa-power-off"></i> {{ __('adminlte::adminlte.log_out') }}
                            </a>
                            <form id="logout-form" action="{{ url(config('adminlte.logout_url', 'auth/logout')) }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                        @if(config('adminlte.right_sidebar') and (config('adminlte.layout') != 'top-nav'))
                        <!-- Control Sidebar Toggle Button -->
                            <li>
                                <a href="#" data-toggle="control-sidebar" @if(!config('adminlte.right_sidebar_slide')) data-controlsidebar-slide="false" @endif>
                                    <i class="{{config('adminlte.right_sidebar_icon')}}"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                @if(config('adminlte.layout') == 'top-nav')
                </div>
                @endif
            </nav>
        </header>

        @if(config('adminlte.layout') != 'top-nav')
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- Sidebar Menu -->
                <ul class="sidebar-menu" data-widget="tree">
                    @each('adminlte::partials.menu-item', $adminlte->menu(), 'item')
                </ul>
                <!-- /.sidebar-menu -->
            </section>
            <!-- /.sidebar -->
        </aside>
        @endif

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @if(config('adminlte.layout') == 'top-nav')
            <div class="container">
            @endif

            <!-- Content Header (Page header) -->
            <section class="content-header">

                @yield('content_header')
                @if (Route::currentRouteName() && Breadcrumbs::exists(Route::currentRouteName()))
                    {{ Breadcrumbs::render() }}
                @endif

            </section>

            <!-- Main content -->
            <section class="content">
            
                @yield('content')

            </section>
            <!-- /.content -->
            @if(config('adminlte.layout') == 'top-nav')
            </div>
            <!-- /.container -->
            @endif
        </div>
        <!-- /.content-wrapper -->

        @hasSection('footer')
        <footer class="main-footer">
            @yield('footer')
        </footer>
        @endif

        @if(config('adminlte.right_sidebar') and (config('adminlte.layout') != 'top-nav'))
            <aside class="control-sidebar control-sidebar-{{config('adminlte.right_sidebar_theme')}}">
                @yield('right-sidebar')
            </aside>
            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>
        @endif

    </div>
    <!-- ./wrapper -->
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src='https://cdn.rawgit.com/jashkenas/underscore/1.8.3/underscore-min.js' type='text/javascript'></script>
    <script src='http://podio.github.io/jquery-mentions-input/lib/jquery.events.input.js' type='text/javascript'></script>
    <script src='http://podio.github.io/jquery-mentions-input/lib/jquery.elastic.js' type='text/javascript'></script>
    <script src='http://podio.github.io/jquery-mentions-input/jquery.mentionsInput.js' type='text/javascript'></script>

    
        <script src="https://adminlte.io/themes/AdminLTE/bower_components/ckeditor/ckeditor.js"></script>
        <script>    
            
            $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replaceAll('form-control');
    
   
  })
        </script>
    
    <script>
        $('a[data-notif-id]').click(function (e) {
            e.preventDefault();
            var notif_id = $(this).data('notifId');
            var targetHref = $(this).attr('href');

            $.get('/markAsRead', {'notif_id': notif_id}, function (data) {
                window.location.href = targetHref;
            }, 'json');
        });

        // Funcionalidade do Changelog
        $(document).ready(function() {
            $('#changelogBtn').on('click', function(e) {
                e.preventDefault();
                
                const funMessages = [
                    "Sacrificando noites de sono e litros de café para trazer novidades...",
                    "Buscando atualizações feitas sob a luz da lua e muita cafeína...",
                    "Carregando código escrito enquanto os pássaros já começavam a cantar...",
                    "Transformando cafeína em melhorias para você. Aguarde...",
                    "Compilando novidades preparadas nas madrugadas mais produtivas...",
                    "Interceptando pacotes de código enviados do futuro (ou de uma madrugada longa)...",
                    "Ajustando os últimos pixels enquanto o sol ameaça aparecer no horizonte...",
                    "Lutando contra o sono e bugs noturnos para entregar essa atualização..."
                ];
                const randomMessage = funMessages[Math.floor(Math.random() * funMessages.length)];

                Swal.fire({
                    title: randomMessage,
                    allowOutsideClick: false,
                    onBeforeOpen: () => { Swal.showLoading(); }
                });

                const webhookUrl = 'https://n8n.srv1477025.hstgr.cloud/webhook/d003ad03-43ca-43ff-af8d-d7baaa195eb6';
                
                fetch(webhookUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        request_time: new Date().toISOString(),
                        action: 'get_changelog'
                    })
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Erro na resposta do servidor: ' + res.status);
                        return res.text(); // Pega como texto primeiro para evitar erro de JSON vazio
                    })
                    .then(text => {
                        console.log('Raw response:', text);
                        
                        if (!text || text.trim() === "" || text === "Workflow started") {
                            // Se for n8n em modo teste, às vezes ele retorna apenas um texto de confirmação
                            // ou vazio se não houver resposta imediata configurada
                            throw new Error('O webhook respondeu com sucesso, mas não enviou dados. Certifique-se de que o workflow do n8n termina com um nó de "Respond to Webhook".');
                        }

                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            throw new Error('A resposta do servidor não é um JSON válido: ' + text.substring(0, 100));
                        }
                        
                        console.log('Parsed data:', data);
                        
                        let contentHtml = '<div style="text-align: left; font-family: \'Source Sans Pro\', sans-serif; max-height: 400px; overflow-y: auto;">';
                        
                        // Robustez para diferentes formatos de JSON (incluindo o novo formato com 'output')
                        let updates = [];
                        let sourceData = data;
                        
                        // Lidar com array envolvente [ { output: ... } ]
                        if (Array.isArray(data) && data.length > 0) {
                            sourceData = data[0];
                        }
                        
                        // Lidar com campo 'output' aninhado
                        if (sourceData.output) {
                            sourceData = sourceData.output;
                        }

                        if (Array.isArray(sourceData)) {
                            updates = sourceData;
                        } else if (sourceData.changelog && Array.isArray(sourceData.changelog)) {
                            updates = sourceData.changelog;
                        } else if (sourceData.updates && Array.isArray(sourceData.updates)) {
                            updates = sourceData.updates;
                        } else if (typeof sourceData === 'object' && sourceData !== null) {
                            // Se for apenas um objeto com mensagem de erro do n8n
                            if (sourceData.message && sourceData.message.includes('not registered')) {
                                throw new Error('O webhook de teste não está ativo no n8n.');
                            }
                            updates = [sourceData];
                        }
                        
                        if (updates.length > 0 && updates[0] !== null) {
                            updates.forEach((item, index) => {
                                let title, description, date;
                                
                                if (typeof item === 'string') {
                                    title = `Item ${index + 1}`;
                                    description = item;
                                    date = '';
                                } else {
                                    title = item.titulo || item.title || item.header || 'Nova Atualização';
                                    description = item.descricao || item.description || item.texto || item.output || JSON.stringify(item);
                                    date = item.updated_at || item.data || item.date || item.timestamp || '';
                                }

                                contentHtml += `
                                    <div style="margin-bottom: 15px; border-left: 5px solid #f39c12; padding-left: 15px; padding-bottom: 10px; background: #fff; padding-top: 10px; border-radius: 0 4px 4px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-top: 1px solid #eee; border-right: 1px solid #eee;">
                                        <div style="font-weight: bold; color: #333; font-size: 14px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f4f4f4; padding-bottom: 5px; margin-bottom: 8px;">
                                            <span><i class="fa fa-caret-right text-orange"></i> ${title}</span>
                                            ${date ? `<small class="label label-default" style="font-size: 10px;">${date}</small>` : ''}
                                        </div>
                                        <div style="color: #555; font-size: 13px; line-height: 1.6;">
                                            ${description}
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            contentHtml += `
                                <div style="text-align: center; padding: 20px; color: #777;">
                                    <i class="fa fa-info-circle" style="font-size: 30px; margin-bottom: 10px; color: #ddd;"></i>
                                    <p>Nenhuma atualização encontrada na última semana.</p>
                                </div>
                            `;
                        }
                        
                        // Extrai a data global do JSON se existir, ou do primeiro item
                        const globalDate = sourceData.date || sourceData.data || sourceData.last_update || (updates.length > 0 ? (updates[0].date || updates[0].data) : null);
                        const displayDate = globalDate || new Date().toLocaleDateString('pt-BR');
                        
                        contentHtml += '</div>';

                        Swal.fire({
                            title: `<i class="fa fa-bullhorn text-yellow"></i> Changelog do Sistema <br><small style="font-size: 12px; color: #777;">Atualizado em: ${displayDate}</small>`,
                            html: contentHtml,
                            width: '650px',
                            confirmButtonColor: '#3c8dbc',
                            confirmButtonText: 'FECHAR',
                            type: 'info'
                        });
                    })
                    .catch(err => {
                        console.error('Erro ao buscar changelog:', err);
                        Swal.fire({
                            title: 'Ops!',
                            text: err.message || 'Não foi possível carregar as atualizações. Verifique se o webhook do n8n está ativo.',
                            type: 'warning',
                            confirmButtonColor: '#f39c12'
                        });
                    });
            });
        });
    </script>
    
    @stack('js')
    @yield('js')
@stop
