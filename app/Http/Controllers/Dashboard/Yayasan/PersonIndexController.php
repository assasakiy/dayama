<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Yayasan;

use App\Http\Controllers\Controller;
use Modules\Yayasan\Models\PersonIndex;
use Inertia\Inertia;

class PersonIndexController extends Controller
{
    public function index()
    {
        $persons = PersonIndex::orderBy('nama_lengkap')
            ->paginate(25);

        return Inertia::render('Yayasan/PersonIndex/Index', [
            'persons' => $persons,
        ]);
    }
}
