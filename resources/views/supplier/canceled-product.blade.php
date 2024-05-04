@extends('adminlte::page')


@section('content')

    <div id="app">
        <canceled-product-component></canceled-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
