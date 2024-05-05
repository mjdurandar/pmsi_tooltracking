@extends('adminlte::page')


@section('content')

    <div id="app">
        <admin-products-component></admin-products-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
