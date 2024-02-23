@extends('adminlte::page')


@section('content')

    <div id="app">
        <request-product-component></request-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
