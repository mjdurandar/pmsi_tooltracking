@extends('adminlte::page')

@section('content')
    <div id="app">
        <qr-component :data="{{ $data }}"></qr-component>
    </div>
@stop

@section('adminlte_css')
    @stack('css')
    @yield('css')
    <style>
        .main-sidebar {
            display: none !important; /* Hide the main sidebar */
        }
        .content-wrapper {
            margin-left: 0 !important; /* Adjust content wrapper margin */
        }
        .main-header {
            display: none !important; /* Hide the main header */
        }
    </style>
@stop

@section('adminlte_js')
    <script src="{{ asset('js/app.js') }}"></script>
@stop
