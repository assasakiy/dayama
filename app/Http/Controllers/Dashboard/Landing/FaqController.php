<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Landing;

use Modules\Landing\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController
{
    public function index(): Response
    {
        $faqs = Faq::orderBy('sort_order')->get();

        return Inertia::render('Landing/Faqs/Index', [
            'faqs' => $faqs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question'   => ['required', 'string', 'max:500'],
            'answer'     => ['required', 'string'],
            'category'   => ['nullable', 'string', 'max:50'],
            'is_active'  => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $data['category'] = $data['category'] ?: 'umum';

        $data['sort_order'] = $data['sort_order'] ?? (Faq::max('sort_order') + 1);

        Faq::create($data);

        return redirect()->route('dashboard.landing.faqs.index')
            ->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $request->validate([
            'question'   => ['required', 'string', 'max:500'],
            'answer'     => ['required', 'string'],
            'category'   => ['nullable', 'string', 'max:50'],
            'is_active'  => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $data['category'] = $data['category'] ?: 'umum';

        $faq->update($data);

        return redirect()->route('dashboard.landing.faqs.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('dashboard.landing.faqs.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }

    /**
     * Reorder FAQs via drag-and-drop.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['string'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            Faq::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return redirect()->route('dashboard.landing.faqs.index')
            ->with('success', 'Urutan FAQ berhasil diperbarui.');
    }
}
