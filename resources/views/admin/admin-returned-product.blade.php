@extends('adminlte::page')


@section('content')

    <div id="app">
        <admin-returned-product-component></admin-returned-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
