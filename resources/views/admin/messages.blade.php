@extends('adminlte::page')


@section('content')

    <div id="app">
        <messages-component></messages-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
