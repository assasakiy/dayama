<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController
{
    public function markAllAsRead(Request $request): RedirectResponse
    {
        if ($request->user()) {
            $request->user()->unreadNotifications->markAsRead();
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
