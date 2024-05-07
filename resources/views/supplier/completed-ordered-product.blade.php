@extends('adminlte::page')


@section('content')

    <div id="app">
        <completed-ordered-product-component></completed-ordered-product-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
