@extends('adminlte::page')


@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>You are logged in!</p>
    <h1>{{$user = Auth::user()->name}}</h1>
@stop
