@extends('adminlte::page')


@section('content')

    <div id="app">
        <completed-order-user-component></completed-order-user-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
