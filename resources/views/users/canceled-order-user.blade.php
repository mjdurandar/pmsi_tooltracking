@extends('adminlte::page')


@section('content')

    <div id="app">
        <canceled-order-user-component></canceled-order-user-component>
    </div>


@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
