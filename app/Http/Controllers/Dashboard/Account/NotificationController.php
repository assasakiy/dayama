<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(\Illuminate\Http\Request $request): Response
    {
        return Inertia::render('Account/Notifications/Index', [
            'preferences' => $request->user()->preferences ?? [],
        ]);
    }

    public function update(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'email_newsletter' => ['required', 'boolean'],
            'email_updates' => ['required', 'boolean'],
            'email_marketing' => ['required', 'boolean'],
            'push_comments' => ['required', 'boolean'],
            'push_mentions' => ['required', 'boolean'],
            'push_messages' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $preferences = $user->preferences ?? [];
        $preferences['email_newsletter'] = $validated['email_newsletter'];
        $preferences['email_updates'] = $validated['email_updates'];
        $preferences['email_marketing'] = $validated['email_marketing'];
        $preferences['push_comments'] = $validated['push_comments'];
        $preferences['push_mentions'] = $validated['push_mentions'];
        $preferences['push_messages'] = $validated['push_messages'];

        $user->preferences = $preferences;
        $user->save();

        return back()->with('success', 'Notification preferences updated successfully.');
    }
}
