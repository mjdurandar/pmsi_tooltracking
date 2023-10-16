@extends('adminlte::page')


@section('content')

    <div id="app">
        <borrowedhistory-component></borrowedhistory-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
