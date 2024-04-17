<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        PMSI ToolTracking
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcPwNsHvqKWVPCBhLnN8-eTY2DZIrnGFc&loading=async&libraries=places&callback=initMap" async defer></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDcPwNsHvqKWVPCBhLnN8-eTY2DZIrnGFc&callback=Function.prototype" async defer></script> -->

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

        @if(config('adminlte.google_fonts.allowed', true))
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        @endif
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
        @if(intval(app()->version()) >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('images/pmsi_logo.png') }}" />

</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @else
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @endif

    {{-- Extra Configured Plugins Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
        @if(intval(app()->version()) >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

</body>

</html>

<style> 
    /* FONT STYLE FOR ALL */
    *{
        font-family: 'Poppins', sans-serif;
    }
    /* LEFT SIDEBAR DESIGN */
    .main-sidebar{
        background-color: #fff;
    }
    /* HORIZONTAL LINE BELOW LOGO */
    [class*=sidebar-dark] .brand-link {
        border: none !important;
    }
    /* SIDEBAR BOX SHADOW */
    .elevation-4{
        box-shadow: none !important;
        border-right: 0.5px solid #CDCDCD;
    }
    /* ON HOVER OF SIDEBAR LINKS */
    .sidebar .nav-link{
        color: black !important;
    }
    .sidebar a:hover{
        background: #f18f4e !important;
        border-radius: 20px !important;
        color: #fff !important;
    }
    .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active{
        background: #f18f4e  !important;
        border-radius: 20px !important;
        color: #fff !important;
    }
    [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active{
        background: #f18f4e !important;
        border-radius: 20px !important;
        color: #fff !important;
    }
    .sidebar a:focus{
        background: #f18f4e !important;
        border-radius: 20px !important;
        color: #fff !important;
    }
    /* TOP NAV BG COLOR */
    .main-header{
        background: #f18f4e !important;
    }
    /* TOP NAV COLOR */
    .navbar-light .navbar-nav .nav-link{
        color: #fff !important;
    }
    /* BACKGROUND COLOR */
    .content-wrapper{
        background: #F9F9F9 !important;
    }


</style>
