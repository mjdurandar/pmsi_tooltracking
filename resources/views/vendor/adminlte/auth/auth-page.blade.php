@extends('adminlte::master')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body'){{ ($auth_type ?? 'login') . '-page' }}@stop
<style>
    body {
        background-image: url("/images/pmsi_bg_dark.jpeg");
        background-size: 100%;
        background-position: bottom;
        background-repeat: no-repeat;
        opacity: 2;
    }
    .logo-login-pmsi{
        text-align: center;
        margin-bottom: 370px;
        position: absolute;
        z-index: 5;
    }
    .logo-pmsi{
        width: 150px;
        height: 150px;
    }
</style>
@section('body')
    <div class="logo-login-pmsi">
        <img src="/images/pmsi_logo.png" alt="PMSI LOGO" class="logo-pmsi">
    </div>
    <div class="{{ $auth_type ?? 'login' }}-box">
        {{-- Card Box --}}
        <div class="card">

            {{-- Card Header --}}
            @hasSection('auth_header')
                <div class="card-header {{ config('adminlte.classes_auth_header', '') }}">
                    <!-- <h3 class="card-title float-none text-center">
                        PMSI TOOLTRACKING
                    </h3> -->
                </div>
            @endif

            {{-- Card Body --}}
            <div class="card-body {{ $auth_type ?? 'login' }}-card-body {{ config('adminlte.classes_auth_body', '') }}">
                @yield('auth_body')
            </div>

            {{-- Card Footer --}}
            @hasSection('auth_footer')
                <div class="card-footer {{ config('adminlte.classes_auth_footer', '') }}">
                    @yield('auth_footer')
                </div>
            @endif

        </div>

    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
