@extends('adminlte::page')


@section('content')

    <div id="app">
        <ordered-product-component></ordered-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
