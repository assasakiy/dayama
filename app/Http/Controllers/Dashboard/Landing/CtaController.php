<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Landing;

use Modules\Landing\Models\Cta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CtaController
{
    public function index(): Response
    {
        $ctas = Cta::latest()->get();

        return Inertia::render('Landing/Ctas/Index', [
            'ctas' => $ctas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'subtitle'    => ['nullable', 'string'],
            'button_text' => ['required', 'string', 'max:100'],
            'button_url'  => ['nullable', 'url', 'max:500'],
            'is_active'   => ['boolean'],
        ]);

        Cta::create($data);

        return redirect()->route('dashboard.landing.ctas.index')
            ->with('success', 'CTA berhasil ditambahkan.');
    }

    public function update(Request $request, Cta $cta): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'title'       => ['required', 'string', 'max:255'],
            'subtitle'    => ['nullable', 'string'],
            'button_text' => ['required', 'string', 'max:100'],
            'button_url'  => ['nullable', 'url', 'max:500'],
            'is_active'   => ['boolean'],
        ]);

        $cta->update($data);

        return redirect()->route('dashboard.landing.ctas.index')
            ->with('success', 'CTA berhasil diperbarui.');
    }

    public function destroy(Cta $cta): RedirectResponse
    {
        $cta->delete();

        return redirect()->route('dashboard.landing.ctas.index')
            ->with('success', 'CTA berhasil dihapus.');
    }
}
