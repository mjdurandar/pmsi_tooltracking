@extends('adminlte::page')


@section('content')

    <div id="app">
        <home-component :role="{{ $role }}" :notifications="{{ $notifications }}" :completed-order="{{ json_encode($completedOrder) }}"
        :returned-products="{{ json_encode($returnedProducts)}}" ></home-component>
    </div>

@stop


@section('js')

    <script src="{{ asset('js/app.js') }}"></script>
  
@stop
