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
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', (string) $request->cpf),
            'telefone' => preg_replace('/[^0-9]/', '', (string) $request->telefone),
        ]);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf'      => ['required', 'string', 'size:11', 'regex:/^[0-9]+$/', 'unique:'.User::class],
            'telefone' => ['required', 'string', 'size:11', 'regex:/^[0-9]+$/'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'cpf'      => $request->cpf,
            'telefone' => $request->telefone,
            'cargo'    => 'cliente', // Define automaticamente como cliente
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
