@extends('adminlte::page')


@section('content')

    <div id="app">
        <product-component></product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
