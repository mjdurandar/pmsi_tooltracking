@extends('adminlte::page')


@section('content')

    <div id="app">
        <add-balance-component></add-balance-component> 
    </div>

@stop

@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
