<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\Yayasan;

use App\Http\Controllers\Controller;
use Modules\Yayasan\Models\PersonTransferLog;
use Inertia\Inertia;

class TransferLogController extends Controller
{
    public function index()
    {
        $logs = PersonTransferLog::with([
            'fromInstitution',
            'toInstitution',
            'sourcePerson',
            'destinationPerson',
            'trigger',
        ])->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Yayasan/TransferLogs/Index', [
            'logs' => $logs,
        ]);
    }
}
