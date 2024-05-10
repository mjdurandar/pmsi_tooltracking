@extends('adminlte::page')


@section('content')

    <div id="app">
        <returning-products-component></returning-products-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
