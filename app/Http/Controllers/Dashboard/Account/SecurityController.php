<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Account\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class SecurityController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        return Inertia::render('Account/Security/Index', [
            'status' => session('status'),
            'two_factor_enabled' => $user->isTwoFactorEnabled(),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()->route('dashboard.account.security.index')
            ->with('success', 'Password updated successfully.');
    }

    public function enableTwoFactor(Request $request)
    {
        $user = $request->user();
        
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $user->update([
            'two_factor_secret' => $secret,
        ]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return response()->json([
            'qr_code_svg' => $qrCodeSvg,
            'secret' => $secret,
        ]);
    }

    public function confirmTwoFactor(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        $google2fa = new Google2FA();

        $secret = $user->two_factor_secret;

        $valid = $google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user->update([
                'two_factor_confirmed_at' => now(),
            ]);

            return back()->with('success', 'Two-Factor Authentication has been enabled.');
        }

        return back()->withErrors(['code' => 'The provided two-factor authentication code was invalid.']);
    }

    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();

        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return back()->with('success', 'Two-Factor Authentication has been disabled.');
    }
}
