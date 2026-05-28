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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:users', 'max:254'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = DB::transaction(function() use($validatedFormInfo){
            return User::create($validatedFormInfo);
        });

        Auth::login($user);

        return redirect('/showtimes');
    }
}
