<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectUser(Auth::user());
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function showRegisterSchool()
    {
        return view('auth.register-school');
    }

    public function registerSchool(Request $request)
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_code' => ['nullable', 'string', 'max:50', 'unique:schools,code'],
            'school_email' => ['required', 'email', 'max:255', 'unique:schools,email'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $school = School::create([
            'name' => $validated['school_name'],
            'code' => $validated['school_code'] ?? strtoupper(substr(md5(uniqid()), 0, 6)),
            'email' => $validated['school_email'],
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'role' => 'admin',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'School account created successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectUser(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    }
}
