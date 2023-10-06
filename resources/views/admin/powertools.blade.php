@extends('adminlte::page')


@section('content')

    <div id="app">
        <powertools-component></powertools-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
