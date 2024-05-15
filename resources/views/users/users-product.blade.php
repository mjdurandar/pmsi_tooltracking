@extends('adminlte::page')


@section('content')

    <div id="app">
        <users-product-component></users-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
