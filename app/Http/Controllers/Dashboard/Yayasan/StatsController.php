<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Yayasan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index()
    {
        $total_institutions = DB::table('core_institutions')->count();
        $total_persons = DB::table('core_persons')->count();
        $total_transfers = DB::table('person_transfer_logs')->count();
        $total_person_index = DB::table('yayasan_person_index')->count();

        return Inertia::render('Yayasan/Stats/Index', [
            'total_institutions' => $total_institutions,
            'total_persons' => $total_persons,
            'total_transfers' => $total_transfers,
            'total_person_index' => $total_person_index,
        ]);
    }
}
