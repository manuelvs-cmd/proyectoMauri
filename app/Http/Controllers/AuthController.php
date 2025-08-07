<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->only('username', 'password');
        
        // Debug temporal
        \Log::info('Intento de login', ['credentials' => $credentials]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            \Log::info('Login exitoso', ['user' => $user->username]);
            return redirect()->intended(route('dashboard'));
        }

        \Log::info('Login fallido');
        return back()->withErrors([
            'username' => 'Credenciales incorrectas.',
        ])->withInput();
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('ok', 'Cuenta creada con éxito..');
    }
}


