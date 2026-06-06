<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store()
    {
        $validatedFormInfo = request()->validate([
            'first_name' => ['required', 'min:2', 'max:64'],
            'last_name' => ['required', 'min:2', 'max:64'],
            'email' => ['required', 'email', 'unique:users', 'max:254'],
            'password' => ['required', 'confirmed', 'min:6', 'max:64'],
        ]);

        $user = DB::transaction(function() use($validatedFormInfo){
            return User::create($validatedFormInfo);
        });

        Auth::login($user);

        return redirect('/showtimes');
    }
}
