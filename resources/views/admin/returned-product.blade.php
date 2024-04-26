@extends('adminlte::page')


@section('content')

    <div id="app">
        <returned-product-component></returned-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
