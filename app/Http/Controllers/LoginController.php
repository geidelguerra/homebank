<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return inertia('auth/Login');
    }

    public function login(LoginRequest $request)
    {
        if (auth()->attempt($request->only(['email', 'password']), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}
