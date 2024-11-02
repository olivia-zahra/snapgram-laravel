<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller {
    // Menampilkan halaman login
    public function showLoginForm() {
        return view('auth.login');
    }

    public function postLogin(Request $request) {
        // Menghandle postLogin
        // Proses autentikasi login akan di proses di dalam
        // fungsi ini
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            // Login berhasil
            return redirect()->route('home');
        }
    
        // Login gagal
        return back();
    }

    public function showRegistrationForm() {
        // Menampilkan halaman registrasi
        return view('auth.register');

        
    }

    public function register(Request $request) {
        // Menghandle proses registrasi
        $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|confirmed|min:8',
        ]);
    
        // Membuat pengguna baru
        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Hash password
        ]);
    
        // Mengalihkan ke halaman login setelah registrasi berhasil
        return redirect()->route('login');
    }

    public function logout(Request $request) {
        // Menghandle logout
        Auth::logout();
        return redirect()->route('login');
    }
}