@extends('adminlte::page')


@section('content')

    <div id="app">
        <supplier1-component></supplier1-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
