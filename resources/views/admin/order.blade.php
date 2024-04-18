@extends('adminlte::page')


@section('content')

    <div id="app">
        <order-component></order-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
