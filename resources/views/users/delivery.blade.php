@extends('adminlte::page')


@section('content')

    <div id="app">
        <delivery-component></delivery-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
