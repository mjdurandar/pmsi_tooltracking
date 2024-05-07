@extends('adminlte::page')


@section('content')

    <div id="app">
        <canceled-ordered-product-component></canceled-ordered-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
