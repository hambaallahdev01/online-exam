<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Rules\TurnstileRule;
use App\Services\TenantDateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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
            'cf-turnstile-response' => [new TurnstileRule],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->hasVerifiedEmail()) {
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

    public function showRegisterSchool(TenantDateTime $tenantDateTime)
    {
        return view('auth.register-school', [
            'timezoneOptions' => $tenantDateTime->timezoneOptions(),
            'defaultTimezone' => $tenantDateTime->timezoneFor(config('tenancy.default_timezone', 'Asia/Jakarta')),
        ]);
    }

    public function registerSchool(Request $request)
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_code' => ['nullable', 'string', 'max:50', 'unique:schools,code'],
            'school_email' => ['required', 'email', 'max:255', 'unique:schools,email'],
            'timezone' => ['required', 'timezone:all'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cf-turnstile-response' => [new TurnstileRule],
        ]);

        $school = School::create([
            'name' => $validated['school_name'],
            'code' => $validated['school_code'] ?? strtoupper(substr(md5(uniqid()), 0, 6)),
            'email' => $validated['school_email'],
            'timezone' => $validated['timezone'],
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'role' => 'admin',
            'password' => Hash::make($validated['password']),
        ]);

        // Send verification email
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        try {
            Mail::send('emails.verification', ['userName' => $user->name, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
                $message->to($user->email)->subject('Verify Your Email Address - Ajenono Exam Platform');
            });
            Log::info("SMTP Dispatch SUCCESS [registerSchool]: Verification email sent to {$user->email}");
        } catch (\Throwable $e) {
            Log::error('SMTP Dispatch FAILURE [registerSchool]: '.$e->getMessage(), [
                'recipient' => $user->email,
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')->with('warning', 'School account created, but the verification email could not be sent. Please try resending it later.');
        }

        return redirect()->route('login')->with('success', 'School account registered successfully! A verification email has been sent to '.$user->email.'. Please verify your email before logging in.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'cf-turnstile-response' => [new TurnstileRule],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            $school = School::where('email', $request->email)->first();
            if ($school) {
                $user = User::where('school_id', $school->id)->where('role', 'admin')->first();
            }
        }

        if (! $user) {
            // Keep the response indistinguishable to prevent account enumeration.
            return back()->with('success', 'If the address is registered, a password reset link has been sent.');
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            Mail::send('emails.reset-password', ['userName' => $user->name, 'resetUrl' => $resetUrl], function ($message) use ($user) {
                $message->to($user->email)->subject('Reset Password Link - Ajenono Exam Platform');
            });
            Log::info("SMTP Dispatch SUCCESS [sendResetLink]: Reset link email sent to {$user->email}");
        } catch (\Throwable $e) {
            Log::error('SMTP Dispatch FAILURE [sendResetLink]: '.$e->getMessage(), [
                'recipient' => $user->email,
                'exception' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['email' => 'The reset email could not be sent. Please try again later.']);
        }

        return back()->with('success', 'If the address is registered, a password reset link has been sent.');
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

        $expiresAt = $record?->created_at
            ? Carbon::parse($record->created_at)
                ->addMinutes((int) config('auth.passwords.users.expire', 60))
            : null;

        if (! $record || ! $expiresAt || $expiresAt->isPast() || ! Hash::check($request->token, $record->token)) {
            if ($record && (! $expiresAt || $expiresAt->isPast())) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            }

            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        $user = User::where('email', $request->email)->first();
        DB::transaction(function () use ($request, $user) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();
        });

        return redirect()->route('login')->with('success', 'Password has been reset successfully! Please sign in with your new password.');
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
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
            'email' => 'required|email',
            'cf-turnstile-response' => [new TurnstileRule],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            $school = School::where('email', $request->email)->first();
            if ($school) {
                $user = User::where('school_id', $school->id)->where('role', 'admin')->first();
            }
        }

        if (! $user) {
            return back()->with('success', 'If the account exists and is unverified, a verification email has been sent.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'If the account exists and is unverified, a verification email has been sent.');
        }

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        try {
            Mail::send('emails.verification', ['userName' => $user->name, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
                $message->to($user->email)->subject('Resend Email Verification - Ajenono Exam Platform');
            });
            Log::info("SMTP Dispatch SUCCESS [resendVerification]: Verification link sent to {$user->email}");
        } catch (\Throwable $e) {
            Log::error('SMTP Dispatch FAILURE [resendVerification]: '.$e->getMessage(), [
                'recipient' => $user->email,
                'exception' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['email' => 'The verification email could not be sent. Please try again later.']);
        }

        return back()->with('success', 'If the account exists and is unverified, a verification email has been sent.');
    }

    public function changeUnverifiedEmailAndResend(Request $request)
    {
        $request->validate([
            'old_email' => 'required|email',
            'password' => 'required|string',
            'new_email' => 'required|email|max:255',
            'cf-turnstile-response' => [new TurnstileRule],
        ]);

        $user = User::where('email', $request->old_email)->first();
        if (! $user) {
            $school = School::where('email', $request->old_email)->first();
            if ($school) {
                $user = User::where('school_id', $school->id)->where('role', 'admin')->first();
            }
        }

        if (! $user) {
            return back()->withErrors(['old_email' => 'The account details could not be verified.']);
        }

        if ($user->hasVerifiedEmail()) {
            return back()->withErrors(['old_email' => 'The account details could not be verified.']);
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['old_email' => 'The account details could not be verified.']);
        }

        if (User::where('email', $request->new_email)->exists()) {
            return back()->withErrors(['new_email' => 'This email address is already in use.']);
        }

        // Update email to new email address
        $user->update(['email' => $request->new_email]);

        // Send verification email to new address
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        try {
            Mail::send('emails.verification', ['userName' => $user->name, 'verifyUrl' => $verifyUrl], function ($message) use ($user) {
                $message->to($user->email)->subject('Verify Your New Email Address - Ajenono Exam Platform');
            });
            Log::info("SMTP Dispatch SUCCESS [changeUnverifiedEmailAndResend]: Updated email verification sent to {$user->email}");
        } catch (\Throwable $e) {
            Log::error('SMTP Dispatch FAILURE [changeUnverifiedEmailAndResend]: '.$e->getMessage(), [
                'recipient' => $user->email,
                'exception' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['new_email' => 'Email updated, but the verification message could not be sent. Please try resending it later.']);
        }

        return redirect()->route('login')->with('success', 'Email address updated successfully! A new verification link has been sent to '.$user->email.'. Please verify your email before logging in.');
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
