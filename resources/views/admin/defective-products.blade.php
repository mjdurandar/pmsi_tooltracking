@extends('adminlte::page')


@section('content')

    <div id="app">
        <defective-products-component></defective-products-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
