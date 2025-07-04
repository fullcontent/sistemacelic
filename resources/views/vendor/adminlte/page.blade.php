@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet"
          href="{{ asset('vendor/adminlte/dist/css/skins/skin-' . config('adminlte.skin', 'blue') . '.min.css')}} ">
    <link rel="stylesheet"
          href="{{ asset('vendor/mentions/jquery.mentions.css')}}">

    @stack('css')
    @yield('css')
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
            @endif
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">



        <ul class="nav navbar-nav">



                     <!-- MENÇOES DO USUARIO -->
        <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="glyphicon glyphicon-comment"></i>
              <span class="label label-info">{{count(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned'))}}</span>
            </a>
                <ul class="dropdown-menu">
                       

              <li class="header">Você foi mencionado {{count(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned'))}} vezes.</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  
                  <!--
                        List notifications
                   -->
                
                   @foreach(auth()->user()->unreadNotifications->where('type','App\Notifications\UserMentioned') as $n)
                        <li>
                    <a href="{{$n->data['action']}}" data-notif-id="{{$n->id}}">
                      <i class="glyphicon glyphicon-comment text-info"></i> {{$n->data['mensagem']}} #{{$n->data['servico']}}
                    </a></li>


                   @endforeach
                  
                </ul>
              </li>
              
              <li class="footer"><a href="{{route('clearMentions')}}">Limpar notificações</a></li>
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
                 {{-- {{Breadcrumbs::render()}} --}}

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
        $('a[data-notif-id]').click(function () {

        var notif_id   = $(this).data('notifId');
        var targetHref = $(this).data('href');
        


        

        $.get('/markAsRead', {'notif_id': notif_id}, function (data) {
            data.success ? (window.location.href = targetHref) : false;
        }, 'json');
        
        return true;
});
    </script>
    
    @stack('js')
    @yield('js')
@stop
