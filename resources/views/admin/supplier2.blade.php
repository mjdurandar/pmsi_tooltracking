@extends('adminlte::page')


@section('content')

    <div id="app">
        <supplier2-component></supplier2-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
