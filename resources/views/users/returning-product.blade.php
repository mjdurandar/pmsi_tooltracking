@extends('adminlte::page')


@section('content')

    <div id="app">
        <returning-product-component></returning-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
