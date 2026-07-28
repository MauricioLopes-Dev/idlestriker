<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->put('guest_user_id', $user->id);
        $request->session()->put('game_launch_pending', true);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('status', 'Conta criada com sucesso!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user instanceof User) {
                $request->session()->put('guest_user_id', $user->id);
            }

            $request->session()->put('game_launch_pending', true);

            return redirect()->intended('/')->with('status', 'Bem-vindo de volta!');
        }

        return back()->withErrors([
            'email' => 'Essas credenciais não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('guest_user_id');
        $request->session()->forget('game_launch_pending');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
