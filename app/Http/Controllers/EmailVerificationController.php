<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        // If user is authenticated and already verified, redirect to dashboard
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Get email from session (set during sign-in/sign-up) or from authenticated user
        $email = $request->session()->get('verification_email') ?? optional($request->user())->email;

        return view('verification-notice', compact('email'));
    }

    public function verify(Request $request, $id, $hash)
    {

        $user = User::findOrFail($id);
        $redirectFlag = $request->session()->pull('redirect_after_verification');
        //  if ($redirectFlag === 'onboarding') {
        //     return redirect('/onboarding/school-setup')
        //         ->with('success', 'Your email has been verified successfully! Let’s onboard your school.');
        // }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($request->hasValidSignature() === false) {
            abort(403, 'Verification link has expired.');
        }

        if ($user->hasVerifiedEmail()) {
             if ($redirectFlag === 'onboarding') {
                return redirect('/onboarding/school-setup')
                    ->with('success', 'Your email has been verified successfully! Let’s onboard your school.');
            }
            return redirect()->route('sign-in')->with('info', 'Your email is already verified. Please sign in.');
        }

        if ($user->markEmailAsVerified()) {
            if ($redirectFlag === 'onboarding') {
                return redirect('/onboarding/school-setup')
                    ->with('success', 'Your email has been verified successfully! Let’s onboard your school.');
            }
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // ✅ Create a team if needed
        if (!$user->ownedTeams()->exists()) {
            $user->ownedTeams()->create(['name' => "{$user->name}'s Team"]);
        }

        // ✅ Check session flag for post-verification redirect



        // Default redirect to sign-in
         if ($redirectFlag === 'onboarding') {
                return redirect('/onboarding/school-setup')
                    ->with('success', 'Your email has been verified successfully! Let’s onboard your school.');
        }
        return redirect()->route('sign-in')->with('success', 'Your email has been verified successfully! You can now sign in.');
    }


    public function send(Request $request)
    {
        // Validate the email input when not authenticated
        if (!$request->user()) {
            $request->validate([
                'email' => 'sometimes|email|exists:users,email'
            ]);
        }

        $user = null;

        // Handle both authenticated and unauthenticated users
        if ($request->user() && !$request->user()->hasVerifiedEmail()) {
            // User is authenticated but not verified
            $user = $request->user();
        } else {
            // User might not be authenticated, try to get from session or request
            $email = $request->input('email') ?? $request->session()->get('verification_email');

            if (!$email) {
                return redirect()->route('sign-in')->with('error', 'Please sign in to request email verification.');
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['We could not find a user with that email address.']
                ]);
            }

            if ($user->hasVerifiedEmail()) {
                $request->session()->forget('verification_email');
                return redirect()->route('sign-in')->with('info', 'Your email is already verified. Please sign in.');
            }
        }

        // Send the verification email
        $user->sendEmailVerificationNotification();

        return back()->with('success', 'A fresh verification link has been sent to your email address.');
    }
}
