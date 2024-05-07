@extends('adminlte::page')


@section('content')

    <div id="app">
        <completed-order-admin-component></completed-order-admin-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
