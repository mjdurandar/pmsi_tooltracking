@extends('adminlte::page')


@section('content')

    <div id="app">
        <product-history-component></product-history-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
