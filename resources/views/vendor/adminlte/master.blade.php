<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title_prefix', config('adminlte.title_prefix', ''))
@yield('title', config('adminlte.title', 'AdminLTE 2'))
@yield('title_postfix', config('adminlte.title_postfix', ''))</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('vendor/Ionicons/css/ionicons.min.css') }}">
    <link rel="apple-touch-icon" sizes="57x57" href="img/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="img/favicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="img/favicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="img/favicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="img/favicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="img/favicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="img/favicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="img/favicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-icon-180x180.png">
<link rel="icon" type="imageimg/favicon/png" sizes="192x192"  href="img/favicon/android-icon-192x192.png">
<link rel="icon" type="imageimg/favicon/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
<link rel="icon" type="imageimg/favicon/png" sizes="96x96" href="img/favicon/favicon-96x96.png">
<link rel="icon" type="imageimg/favicon/png" sizes="16x16" href="img/favicon/favicon-16x16.png">

<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="img/favicon/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

    @include('adminlte::plugins', ['type' => 'css'])

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/AdminLTE.min.css') }}">

    @yield('adminlte_css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-FL7L3REFC4"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-FL7L3REFC4');
</script>
</head>
<body class="hold-transition @yield('body_class')">

@yield('body')

<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery/dist/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>

@include('adminlte::plugins', ['type' => 'js'])

@yield('adminlte_js')

</body>
</html>
