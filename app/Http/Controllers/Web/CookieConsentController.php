<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\CookieConsent\CookieConsentService;
use App\CookieConsent\Enums\ConsentLevel;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    public function store(Request $request, CookieConsentService $service)
    {
        $request->validate([
            'level' => ['required', 'string', 'in:all,necessary'],
        ]);

        if ($request->input('level') === 'all') {
            $service->acceptAll();
        } else {
            $service->acceptNecessary();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}
