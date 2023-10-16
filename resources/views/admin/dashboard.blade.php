@extends('adminlte::page')


@section('content')

    <div id="app">
        <dashboard-component></dashboard-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
