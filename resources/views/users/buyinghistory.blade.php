@extends('adminlte::page')


@section('content')

    <div id="app">
        <buyinghistory-component></buyinghistory-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
