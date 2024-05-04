@extends('adminlte::page')


@section('content')

    <div id="app">
        <track-order-component></track-order-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
