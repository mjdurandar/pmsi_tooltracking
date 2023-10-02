@extends('adminlte::page')


@section('content')

    <div id="app">
        <project-site-component></project-site-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
