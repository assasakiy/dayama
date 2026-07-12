<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ThemeController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        $theme = $request->input('theme', 'light');

        if (!in_array($theme, ['light', 'dark'])) {
            $theme = 'light';
        }

        return response()->json(['success' => true, 'theme' => $theme])
            ->cookie('theme', $theme, 60 * 24 * 365);
    }
}
