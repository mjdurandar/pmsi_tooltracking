@extends('adminlte::page')


@section('content')

    <div id="app">
        <supplier3-component></supplier3-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
