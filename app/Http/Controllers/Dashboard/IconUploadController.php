<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class IconUploadController
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'icon' => ['required', 'file', 'mimes:svg,png,jpg,jpeg,webp', 'max:2048'],
        ]);

        $file = $request->file('icon');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('icons', $filename, 'public');

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }
}
