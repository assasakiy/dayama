<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AppearanceController extends Controller
{
    public function index(\Illuminate\Http\Request $request): Response
    {
        return Inertia::render('Account/Appearance/Index', [
            'preferences' => $request->user()->preferences ?? [],
        ]);
    }

    public function update(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
            'color_scheme' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $preferences = $user->preferences ?? [];
        $preferences['theme'] = $validated['theme'];
        
        if (isset($validated['color_scheme'])) {
            $preferences['color_scheme'] = $validated['color_scheme'];
        }

        $user->preferences = $preferences;
        $user->save();

        return back()->with('success', 'Appearance updated successfully.');
    }
}
