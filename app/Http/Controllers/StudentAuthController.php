<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        return view('student.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nisn' => 'required',
            'password' => 'required',
        ]);

        $student = Student::where('nisn', $credentials['nisn'])->first();

        if (!$student || !$student->is_active) {
            return back()->withErrors([
                'nisn' => 'Akun tidak ditemukan atau tidak aktif.',
            ])->withInput();
        }

        if (Auth::guard('student')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('student.dashboard'));
        }

        return back()->withErrors([
            'nisn' => 'NISN atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
