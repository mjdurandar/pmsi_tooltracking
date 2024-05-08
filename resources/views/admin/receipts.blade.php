@extends('adminlte::page')


@section('content')

    <div id="app">
        <receipts-component></receipts-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
