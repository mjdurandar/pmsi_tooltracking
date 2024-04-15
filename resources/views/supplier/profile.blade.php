@extends('adminlte::page')


@section('content')

    <div id="app">
        <profile-component></profile-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
