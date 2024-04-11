@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form action="{{ $register_url }}" method="post">
        @csrf

        {{-- Name field --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Contact Number field --}}
        <div class="input-group mb-3">
            <input type="text" name="contact_address" class="form-control @error('contact_address') is-invalid @enderror"
                   value="{{ old('contact_address') }}" placeholder="Contact Number">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa-solid fa-hashtag {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div> 

            @error('contact_address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span> 
            @enderror
        </div>

        {{-- Region dropdown --}}
        <div class="input-group mb-3">
            <select name="region_id" class="form-control">
                <option value="" selected disabled>Select Region</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                @endforeach
            </select>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-map-marker-alt"></span>
                </div>
            </div>
        </div>

        {{-- Province dropdown --}}
        <div class="input-group mb-3">
            <select name="province_id" class="form-control">
                <option value="" selected disabled>Select Province</option>
                @foreach($provinces as $province)
                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                @endforeach
            </select>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-map-marker-alt"></span>
                </div>
            </div>
        </div>

        {{-- City field --}}
        <div class="input-group mb-3">
            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                   value="{{ old('city') }}" placeholder="City">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-map-marker-alt"></span>
                </div>
            </div>

            @error('city')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Barangay field --}}
        <div class="input-group mb-3">
            <input type="text" name="barangay" class="form-control @error('barangay') is-invalid @enderror"
                   value="{{ old('barangay') }}" placeholder="Barangay">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-map-marker-alt"></span>
                </div>
            </div>

            @error('barangay')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- House Number field --}}
        <div class="input-group mb-3">
            <input type="text" name="house_number" class="form-control @error('house_number') is-invalid @enderror"
                   value="{{ old('house_number') }}" placeholder="House Number">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-map-marker-alt"></span>
                </div>
            </div>

            @error('house_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation"
                   class="form-control @error('password_confirmation') is-invalid @enderror"
                   placeholder="{{ __('adminlte::adminlte.retype_password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>

    </form>
    
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $login_url }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop
