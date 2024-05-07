@extends('adminlte::page')


@section('content')

    <div id="app">
        <returned-products-supplier-component></returned-products-supplier-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
