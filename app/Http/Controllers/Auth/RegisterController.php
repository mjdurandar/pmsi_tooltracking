<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Supplier;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'contact_person' => ['required', 'string', 'max:255'],
            // 'location' => ['required', 'string', 'max:255'],
            'accounts' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {   
        $role = $data['accounts'] === 'Supplier' ? 2 : 0;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_address' => $data['contact_address'],
            'contact_person' => $data['contact_person'],
            // 'location' => $data['location'],
            'accounts' => $data['accounts'],
            'password' => Hash::make($data['password']),
            'role' => $role,
            'company_description' => $data['company_description'],
        ]);
    
        // If role is 2 (Supplier), associate the user's name with the supplier
        if ($role === 2) {
            $supplier = new Supplier();
            $supplier->name = $data['name']; 
            $supplier->save();
        }
    
        return $user;
    }
}
