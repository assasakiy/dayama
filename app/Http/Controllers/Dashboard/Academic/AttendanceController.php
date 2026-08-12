<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Academic;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Academic/Attendance/Index');
    }
}
