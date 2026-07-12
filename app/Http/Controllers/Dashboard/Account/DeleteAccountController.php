<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DeleteAccountController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Delete/Index');
    }

    public function destroy(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->is_protected || $user->is_primary_super_admin) {
            abort(403, 'Your account is protected and cannot be deleted.');
        }
        
        \Illuminate\Support\Facades\Auth::logout();
        
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
