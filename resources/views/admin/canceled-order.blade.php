@extends('adminlte::page')


@section('content')

    <div id="app">
        <canceled-order-component></canceled-order-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
