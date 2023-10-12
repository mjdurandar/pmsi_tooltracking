@extends('adminlte::page')


@section('content')

    <div id="app">
        <buytools-component></buytools-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
