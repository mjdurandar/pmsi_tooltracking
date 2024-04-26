@extends('adminlte::page')


@section('content')

    <div id="app">
        <damaged-return-component></damaged-return-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
