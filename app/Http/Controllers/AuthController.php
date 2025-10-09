<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan form login
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Menampilkan form pendaftaran
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * Proses pendaftaran
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nim' => 'required|string|max:20|unique:users',
            'program_studi' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'agree_terms' => 'required',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'agree_terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'program_studi' => $validated['program_studi'],
            'password' => Hash::make($validated['password']),
        ]);

        // Login user setelah pendaftaran
        Auth::login($user);

        return redirect()->route('upload.form')
            ->with('success', 'Pendaftaran berhasil! Selamat datang di FormatCheck ITS.');
    }

    /**
     * Proses login dengan debugging
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Tentukan apakah input adalah email atau NIM
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';
        
        // Login dengan field yang sesuai
        if (Auth::attempt([$loginType => $request->login, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('upload.form'))
                ->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'login' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('login');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')
            ->with('success', 'Logout berhasil!');
    }
}