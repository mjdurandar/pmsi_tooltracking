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

        {{-- Account Type --}}
        <div class="input-group mb-3">
            <select name="accounts" id="accounts" class="form-control">
                <option value="" selected disabled>Account Type</option>
                <option value="User">User</option>
                <option value="Supplier">Supplier</option>
            </select>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-map-marker-alt"></span>
                </div>
            </div>
        </div>

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

        {{-- Contact Person field --}}
        <div class="input-group mb-3">
            <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror"
                   value="{{ old('contact_person') }}" placeholder="Contact Person">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa-solid fa-users {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div> 

            @error('contact_person')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span> 
            @enderror
        </div>

        {{-- Contact Number field --}}
        <div class="input-group mb-3">
            <input type="number" name="contact_address" maxlength="12" class="form-control @error('contact_address') is-invalid @enderror"
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

        {{-- Location --}}
        <!-- <div class="input-group mb-3">
            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                   value="{{ old('location') }}" placeholder="Enter your Location">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fa-solid fa-location {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div> 

            @error('location')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span> 
            @enderror
        </div> -->

        {{-- Company Description --}}
        <div id="descriptionField" class="mb-3" style="display: none;">
            <textarea name="company_description" class="form-control" placeholder="Company Description" rows="4" cols="40"></textarea>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
            // Listen for changes in the select element
            document.getElementById('accounts').addEventListener('change', function() {
                var descriptionField = document.getElementById('descriptionField');
                // If the selected value is 'Supplier', show the description input; otherwise, hide it
                if (this.value === 'Supplier') {
                    descriptionField.style.display = 'block';
                } else {
                    descriptionField.style.display = 'none';
                }
            });
        });
    // Define the initialize function
    function initialize() {
        var input = document.getElementById('location');
        var options = {
            types: ['address'], // Limit results to addresses
            componentRestrictions: {country: 'PH'} // Restrict results to a specific country (e.g., United States)
        };
        var autocomplete = new google.maps.places.Autocomplete(input, options);
        
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            console.log('Selected Place:', place);
            // Handle the selected place
        });
    }
</script>