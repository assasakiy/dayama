<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $homepageType = SettingService::get('pages.homepage_type', 'blog');

        if ($homepageType === 'custom') {
            return view('web.landing.index');
        }

        // If 'blog', we fallback to the blog home logic.
        return app(\App\Http\Controllers\Web\HomeController::class)->__invoke();
    }
}
