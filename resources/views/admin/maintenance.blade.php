@extends('adminlte::page')


@section('content')

    <div id="app">
        <maintenance-component></maintenance-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
