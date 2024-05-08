@extends('adminlte::page')


@section('content')

    <div id="app">
        <complete-order-admin-product-component></complete-order-admin-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
