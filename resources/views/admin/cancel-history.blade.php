@extends('adminlte::page')


@section('content')

    <div id="app">
        <cancel-history-component></cancel-history-component>
    </div>

@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
