<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SignInController extends Controller
{
    public function create()
    {
        return view('login');
    }

    public function store(SignInRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Rate limiting for login attempts
        $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        if (auth()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($key);

            $user = auth()->user();

            // Check if user's email is verified
            if (! $user->hasVerifiedEmail()) {
                // Log out the user immediately
                auth()->logout();

                // Regenerate session to prevent session fixation
                $request->session()->regenerate();

                // Store the user's email in session for the verification notice page
                $request->session()->put('verification_email', $user->email);

                // Redirect to email verification notice
                return redirect()->route('verification.notice')
                    ->with('info', 'Please verify your email address before continuing.');
            }

            // Check if user account is suspended
            if ($user->status === 'suspended') {
                // Log out the user immediately
                auth()->logout();

                // Regenerate session to prevent session fixation
                $request->session()->regenerate();

                // Redirect to suspended account page
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Your account has been suspended. Please contact the administrator for more information.',
                    ]);
            }

            // Check if OTP is enabled in environment
            $otpEnabled = config('app.enable_otp', false) || env('ENABLE_OTP', false);

            if ($otpEnabled) {
                // Generate and send 2FA code
                $this->generate2faCode($user);

                // Store remember preference before logout
                $remember = $request->boolean('remember');

                // Log out until 2FA is verified
                auth()->logout();

                // Store user info in session for 2FA process
                $request->session()->put([
                    '2fa:user:id' => $user->id,
                    '2fa:user:email' => $user->email,
                    '2fa:attempts' => 0,
                    '2fa:last_resend' => null,
                    'auth.remember' => $remember, // Store remember preference
                ]);

                return redirect()->route('2fa.verify');
            } else {
                // OTP is disabled, proceed directly to dashboard
                $request->session()->regenerate();

                return redirect()->intended('dashboard');
            }
        }

        RateLimiter::hit($key);

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->onlyInput('email');
    }

    public function show2faForm()
    {
        // Check if OTP is enabled
        $otpEnabled = config('app.enable_otp', false) || env('ENABLE_OTP', false);

        if (! $otpEnabled) {
            return redirect()->route('login')->withErrors([
                'email' => 'Two-factor authentication is not enabled.',
            ]);
        }

        if (! session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('2fa');
    }

    public function verify2fa(Request $request)
    {
        // Check if OTP is enabled
        $otpEnabled = config('app.enable_otp', false) || env('ENABLE_OTP', false);

        if (! $otpEnabled) {
            return redirect()->route('login')->withErrors([
                'email' => 'Two-factor authentication is not enabled.',
            ]);
        }

        $request->validate(['code' => 'required|digits:6']);

        // Check if user session exists
        if (! session('2fa:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Session expired. Please sign in again.',
            ]);
        }

        // Rate limiting for 2FA attempts
        $attempts = session('2fa:attempts', 0);

        if ($attempts >= 5) {
            // Clear session and redirect to sign in
            $request->session()->forget(['2fa:user:id', '2fa:user:email', '2fa:attempts', '2fa:last_resend']);

            return redirect()->route('login')->withErrors([
                'email' => 'Too many failed attempts. Please sign in again.',
            ]);
        }

        // Load user with role relationship and refresh from database
        $user = User::with('primaryRole')->find(session('2fa:user:id'));

        if (! $user) {
            $request->session()->forget(['2fa:user:id', '2fa:user:email', '2fa:attempts', '2fa:last_resend']);

            return redirect()->route('login')->withErrors([
                'email' => 'User not found. Please sign in again.',
            ]);
        }

        // Force refresh from database to get latest values
        $user->refresh();

        // Double-check email verification before completing 2FA
        if (! $user->hasVerifiedEmail()) {
            // Clear 2FA session
            $request->session()->forget(['2fa:user:id', '2fa:user:email', '2fa:attempts', '2fa:last_resend']);

            // Store email for verification notice
            $request->session()->put('verification_email', $user->email);

            return redirect()->route('verification.notice')
                ->with('info', 'Please verify your email address before continuing.');
        }

        // Check if user account is suspended
        if ($user->status === 'suspended') {
            // Clear 2FA session
            $request->session()->forget(['2fa:user:id', '2fa:user:email', '2fa:attempts', '2fa:last_resend']);

            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Your account has been suspended. Please contact the administrator for more information.',
                ]);
        }

        // Check if code exists and is not expired
        if (! $user->two_factor_code || ! $user->two_factor_expires_at) {
            return back()->withErrors(['code' => 'No verification code found. Please request a new one.']);
        }

        if ($user->two_factor_expires_at->lt(now())) {
            return back()->withErrors(['code' => 'The verification code has expired. Please request a new one.']);
        }

        // Verify the code using secure comparison
        if (hash_equals((string) $user->two_factor_code, (string) $request->code)) {
            // Clear 2FA data
            $user->update([
                'two_factor_code' => null,
                'two_factor_expires_at' => null,
            ]);

            // Log the user in with remember preference
            $remember = session('auth.remember', false);
            auth()->login($user, $remember);

            // Clean up session
            $request->session()->forget(['2fa:user:id', '2fa:user:email', '2fa:attempts', '2fa:last_resend', 'auth.remember']);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        // Increment failed attempts
        $request->session()->put('2fa:attempts', $attempts + 1);

        return back()->withErrors(['code' => 'The verification code is incorrect.']);
    }

    public function resend2fa(Request $request)
    {
        // Check if OTP is enabled
        $otpEnabled = config('app.enable_otp', false) || env('ENABLE_OTP', false);

        if (! $otpEnabled) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled.',
            ], 400);
        }

        if (! session('2fa:user:id')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid session',
            ], 400);
        }

        // Rate limiting for resend attempts
        $lastResend = session('2fa:last_resend');
        if ($lastResend && Carbon::parse($lastResend)->addSeconds(60)->gt(now())) {
            $remainingSeconds = Carbon::parse($lastResend)->addSeconds(60)->diffInSeconds(now());

            return response()->json([
                'success' => false,
                'message' => "Please wait {$remainingSeconds} seconds before requesting a new code.",
                'remaining_seconds' => $remainingSeconds,
            ], 429);
        }

        $user = User::find(session('2fa:user:id'));

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Check if user account is suspended
        if ($user->status === 'suspended') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been suspended. Please contact the administrator for more information.',
            ], 403);
        }

        try {
            // Generate and send new code
            $this->generate2faCode($user);

            // Update last resend time
            $request->session()->put('2fa:last_resend', now()->toISOString());

            // Reset attempts counter
            $request->session()->put('2fa:attempts', 0);

            return response()->json([
                'success' => true,
                'message' => 'New verification code sent to your email.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to resend 2FA code', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate and send 2FA code to user
     */
    private function generate2faCode(User $user): void
    {
        // Generate a cryptographically secure random code
        $code = random_int(100000, 999999);

        // Set expiration to 15 minutes from now
        $expiresAt = now()->addMinutes(15);

        // Update user with new code and expiration
        $updated = $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => $expiresAt,
        ]);

        // Verify the update was successful
        if (! $updated) {
            \Log::error('Failed to update 2FA code in database', [
                'user_id' => $user->id,
            ]);
            throw new \Exception('Failed to generate verification code');
        }

        // Refresh model to confirm database changes
        $user->refresh();

        // Send code via email
        try {
            Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($code));
        } catch (\Exception $e) {
            \Log::error('Failed to send 2FA email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
