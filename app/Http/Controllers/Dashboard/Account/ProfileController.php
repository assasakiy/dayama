<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Account\UpdateProfileRequest;
use App\Services\Account\UpdateProfileService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Profile/Index', [
            'status' => session('status'),
        ]);
    }

    public function update(UpdateProfileRequest $request, UpdateProfileService $service): RedirectResponse
    {
        $service->update($request->user(), $request->validated());

        return redirect()->route('dashboard.account.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
