<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view("layouts.admin.guest");
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|email:dns",
            "password" => "required"
        ]);

        $credential = $request->only(["email", "password"]);

        if (Auth::attempt($credential)) {
            if (Auth::check() && Auth::user()->role != "Admin") {
                return redirect()->back()->with("error", "Akun anda bukan akun admin. Dilarang masuk halaman ini");
            }
            $request->session()->regenerate();
            return redirect()->route("admin.dashboard.index")->with("success", "Login berhasil");
        }

        return redirect()->back()->with("error", "Maaf autentikasi gagal. Silahkan coba lagi");
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
