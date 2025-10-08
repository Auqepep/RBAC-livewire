<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified via email link.
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        // Find the user by ID
        $user = User::find($id);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Verify the hash matches the user's email
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        // Check if URL signature is valid (security check)
        if (!URL::hasValidSignature($request)) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            // Auto-login the user if not already authenticated
            if (!auth()->check()) {
                auth()->login($user);
            }
            return redirect()->route('dashboard')->with('success', 'Email already verified.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Auto-login the user after successful verification
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Email verified successfully! You are now logged in.');
    }
}
