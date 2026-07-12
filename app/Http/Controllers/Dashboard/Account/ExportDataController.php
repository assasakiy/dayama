<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ExportDataController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Export/Index');
    }

    public function export(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        
        $data = [
            'profile' => $user->toArray(),
            'preferences' => $user->preferences,
            'emails' => $user->emails()->get()->toArray(),
            'connected_accounts' => $user->connectedAccounts()->get()->toArray(),
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        }, 'user_data_export.json');
    }
}
