@extends('adminlte::page')


@section('content')

    <div id="app">
        <returndays-component></returndays-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
