<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use App\Mail\VerifySecondaryEmail;
use App\Models\UserEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $user->load('emails');

        return Inertia::render('Account/Details/Index', [
            'user'        => $user,
            'emails'      => $user->emails,
            'preferences' => $user->preferences ?? [],
            'flash'       => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string', 'timezone'],
            'language' => ['required', 'string', 'in:en,id,es,fr'],
        ]);

        $user = $request->user();
        $preferences = $user->preferences ?? [];
        $preferences['timezone'] = $validated['timezone'];
        $preferences['language'] = $validated['language'];

        $user->preferences = $preferences;
        $user->save();

        return back()->with('success', 'Regional preferences updated successfully.');
    }

    /**
     * Send a 6-digit OTP code to the given email record.
     */
    private function sendVerificationCode(UserEmail $email): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $email->update([
            'verification_code'            => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
            'verification_sent_at'         => now(),
        ]);

        Mail::to($email->email)->send(new VerifySecondaryEmail($code));

        Log::info("VERIFICATION CODE SENT TO: {$email->email} — CODE: {$code}");

        return $code;
    }

    public function storeEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:user_emails,email'],
        ]);

        $user  = $request->user();
        $email = $user->emails()->create([
            'email'      => $validated['email'],
            'is_primary' => false,
        ]);

        $this->sendVerificationCode($email);

        return back()->with('success', 'Email added! A 6-digit verification code has been sent to your inbox.');
    }

    public function destroyEmail(Request $request, string $id): RedirectResponse
    {
        $user  = $request->user();
        $email = $user->emails()->where('id', $id)->firstOrFail();

        if ($email->is_primary || $user->email === $email->email) {
            return back()->with('error', 'Cannot delete your primary email address.');
        }

        $email->delete();

        return back()->with('success', 'Email address removed.');
    }

    public function setPrimaryEmail(Request $request, string $id): RedirectResponse
    {
        $user  = $request->user();
        $email = $user->emails()->where('id', $id)->firstOrFail();

        if (!$email->email_verified_at) {
            return back()->with('error', 'Please verify this email address before setting it as primary.');
        }

        DB::transaction(function () use ($user, $email) {
            $user->update([
                'email'             => $email->email,
                'email_verified_at' => $email->email_verified_at,
            ]);
            $user->emails()->update(['is_primary' => false]);
            $email->update(['is_primary' => true]);
        });

        return back()->with('success', 'Primary email updated successfully.');
    }

    public function resendVerification(Request $request, string $id): RedirectResponse
    {
        $user  = $request->user();
        $email = $user->emails()->where('id', $id)->firstOrFail();

        if ($email->email_verified_at) {
            return back()->with('success', 'This email is already verified.');
        }

        // Rate limit: 60 seconds between resends
        if ($email->verification_sent_at && now()->diffInSeconds($email->verification_sent_at) < 60) {
            $wait = 60 - now()->diffInSeconds($email->verification_sent_at);
            return back()->with('error', "Please wait {$wait} seconds before requesting another code.");
        }

        $this->sendVerificationCode($email);

        return back()->with('success', 'A new verification code has been sent to your inbox.');
    }

    public function verifyEmailCode(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user  = $request->user();
        $email = $user->emails()->where('id', $id)->firstOrFail();

        if ($email->email_verified_at) {
            return back()->with('success', 'This email is already verified.');
        }

        if (!$email->verification_code) {
            return back()->with('error', 'No verification code found. Please request a new code.');
        }

        if (now()->isAfter($email->verification_code_expires_at)) {
            return back()->with('error', 'Verification code has expired. Please request a new code.');
        }

        if ($email->verification_code !== $request->code) {
            return back()->with('error', 'Incorrect verification code. Please try again.');
        }

        $email->update([
            'email_verified_at'            => now(),
            'verification_code'            => null,
            'verification_code_expires_at' => null,
        ]);

        return back()->with('success', 'Email verified successfully!');
    }
}
