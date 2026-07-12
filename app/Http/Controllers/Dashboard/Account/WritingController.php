<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class WritingController extends Controller
{
    public function index(\Illuminate\Http\Request $request): Response
    {
        return Inertia::render('Account/Writing/Index', [
            'preferences' => $request->user()->preferences ?? [],
        ]);
    }

    public function update(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'default_editor' => ['required', 'string', 'in:markdown,rich_text'],
            'auto_save' => ['required', 'boolean'],
            'spell_check' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $preferences = $user->preferences ?? [];
        $preferences['default_editor'] = $validated['default_editor'];
        $preferences['auto_save'] = $validated['auto_save'];
        $preferences['spell_check'] = $validated['spell_check'];

        $user->preferences = $preferences;
        $user->save();

        return back()->with('success', 'Writing preferences updated successfully.');
    }
}
