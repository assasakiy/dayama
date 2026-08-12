<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Academic\Models\Subject;

class SubjectController extends Controller
{
    public function index(): Response
    {
        $subjects = Subject::query()
            ->orderBy('nama')
            ->get();

        return Inertia::render('Academic/Subjects/Index', [
            'subjects' => $subjects,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Subjects/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:academic_subjects,nama',
            'kode' => 'nullable|string|max:20',
        ]);

        Subject::create($validated);

        return redirect()->to('/academic/subjects');
    }

    public function edit(string $id): Response
    {
        $subject = Subject::findOrFail($id);

        return Inertia::render('Academic/Subjects/Form', [
            'subject' => $subject,
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:academic_subjects,nama,' . $id,
            'kode' => 'nullable|string|max:20',
        ]);

        $subject->update($validated);

        return redirect()->to('/academic/subjects');
    }

    public function destroy(string $id): RedirectResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->to('/academic/subjects');
    }
}
