<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Team;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/tourney-saya';

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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'integer', 'in:1,2'],
            'team_name' => ['required_if:role,2', 'nullable', 'string', 'max:255'],
            'coach' => ['required_if:role,2', 'nullable', 'string', 'max:255'],
            'manager' => ['required_if:role,2', 'nullable', 'string', 'max:255'],
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
        if ($data['role'] == 2) {
            // Buat tim baru
            $team = Team::create([
                'name' => $data['team_name'],
                'coach' => $data['coach'],
                'manager' => $data['manager'],
                'tournaments_id' => null,
            ]);

            // Buat user dengan team_id yang baru dibuat
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 2,
                'team_id' => $team->id,
            ]);
        }

        // Buat user sebagai admin
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 1,
            'team_id' => null,
        ]);
    }
}
