@extends('adminlte::page')


@section('content')

    <div id="app">
        <transactions-component></transactions-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
