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
            'cf-turnstile-response' => [new \App\Rules\TurnstileRule],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => __('Your email address is not verified yet. Please check your inbox for the verification link.'),
                ]);
            }

            $request->session()->regenerate();
            return $this->redirectUser($user);
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
            'cf-turnstile-response' => [new \App\Rules\TurnstileRule],
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

        // Send verification email
        $verifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        try {
            Mail::raw("Halo {$user->name},\n\nTerima kasih telah mendaftarkan sekolah {$school->name} di Ajenono Exam Platform.\n\nSilakan verifikasi email Anda dengan mengklik tautan berikut:\n{$verifyUrl}\n\nSetelah email terverifikasi, Anda dapat login ke platform.\n\nSalam,\nTim Ajenono Exam Platform", function ($message) use ($user) {
                $message->to($user->email)->subject('Verify Your Email Address - Ajenono Exam Platform');
            });
        } catch (\Throwable $e) {
            // Mail fallback
        }

        return redirect()->route('login')->with('success', 'School account registered successfully! A verification email has been sent to ' . $user->email . '. Please verify your email before logging in.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'cf-turnstile-response' => [new \App\Rules\TurnstileRule],
        ]);

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

    public function showResendVerification()
    {
        return view('auth.resend-verification');
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'cf-turnstile-response' => [new \App\Rules\TurnstileRule],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Your email address is already verified. Please sign in.');
        }

        $verifyUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        try {
            Mail::raw("Halo {$user->name},\n\nBerikut adalah tautan verifikasi email baru untuk akun Ajenono Exam Platform Anda:\n{$verifyUrl}\n\nTautan ini berlaku selama 60 menit. Silakan klik untuk menyelesaikan verifikasi email.\n\nSalam,\nTim Ajenono Exam Platform", function ($message) use ($user) {
                $message->to($user->email)->subject('Resend Email Verification - Ajenono Exam Platform');
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => 'Failed to send verification email: ' . $e->getMessage()]);
        }

        return back()->with('success', 'A new verification link has been sent to your email address (' . $user->email . ').');
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
