<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        try {
            Mail::raw("Halo {$user->name},\n\nKami menerima permintaan untuk mereset kata sandi akun Ajenono Exam Platform Anda.\n\nKlik tautan berikut untuk membuat kata sandi baru:\n{$resetUrl}\n\nTautan ini hanya berlaku untuk waktu terbatas. Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini.\n\nSalam,\nTim Ajenono Exam Platform", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Reset Password Link - Ajenono Exam Platform');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'Failed to send reset link email: ' . $e->getMessage()]);
        }

        return back()->with('success', 'We have emailed your password reset link!');
    }

    public function showResetPassword($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password has been reset successfully! Please sign in with your new password.');
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        if (sha1($user->getEmailForVerification()) !== $hash) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('login')->with('success', 'Email address verified successfully!');
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
