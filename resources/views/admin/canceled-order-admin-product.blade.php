@extends('adminlte::page')


@section('content')

    <div id="app">
        <canceled-order-admin-product-component></canceled-order-admin-product-component>
    </div>

@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
