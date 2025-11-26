<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Procesa la solicitud de registro.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validaciones (Aquí corregimos para que coincida con tu formulario)
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], // Antes buscaba 'name'
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'], // Agregamos validación para teléfono
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Creación del Usuario en la BD
        $user = User::create([
            'username' => $request->username, // Guardamos el username
            'email' => $request->email,
            'phone' => $request->phone,       // Guardamos el teléfono
            'role' => 'user',                 // Asignamos rol por defecto
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}