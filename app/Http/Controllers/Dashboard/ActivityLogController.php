<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Authorization\AuthorizationService;
use App\Authorization\VisibilityManager;
use Modules\System\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController
{
    public function __construct(
        private VisibilityManager $visibilityManager
    ) {}

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', ActivityLog::class);

        $user = auth()->user();
        
        // Use the Authorization Domain to get UI capabilities cleanly
        $capabilities = app(AuthorizationService::class)->capabilities($user, ActivityLog::class);
        $seeAll = $capabilities->seeAll();
        $canDelete = $capabilities->delete();

        $query = ActivityLog::with('causer')->latest();
        $query = $this->visibilityManager->apply($query, $user);

        // Filters
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('causer_id') && $seeAll) {
            $query->where('causer_id', $request->causer_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString()->through(fn ($log) => [
            'id'               => $log->id,
            'event'            => $log->event,
            'log_name'         => $log->log_name,
            'description'      => $log->description,
            'subject_type'     => $log->subject_type ? class_basename($log->subject_type) : null,
            'subject_id'       => $log->subject_id,
            'causer'           => $log->causer ? [
                'id'         => $log->causer->id,
                'name'       => $log->causer->name,
                'avatar_url' => $log->causer->avatar_url ?? null,
            ] : null,
            'properties'       => $log->properties,
            'attribute_changes'=> $log->attribute_changes,
            'created_at'       => $log->created_at?->toISOString(),
            'created_at_human' => $log->created_at?->diffForHumans(),
        ]);

        // Events scoped to what the user can see
        $eventsQuery = ActivityLog::query()->distinct()->whereNotNull('event');
        $eventsQuery = $this->visibilityManager->apply($eventsQuery, $user);
        $events = $eventsQuery->orderBy('event')->pluck('event');

        return Inertia::render('ActivityLogs/Index', [
            'logs'    => $logs,
            'events'  => $events,
            'filters' => $request->only(['event', 'causer_id', 'date_from', 'date_to']),
            'can'     => ['see_all' => $seeAll, 'delete' => $canDelete],
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $log = ActivityLog::findOrFail($id);
        
        Gate::authorize('delete', $log);

        $log->delete();

        return back()->with('success', 'Log deleted.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $logs = ActivityLog::whereIn('id', $request->ids)->get();
        $deletedCount = 0;

        foreach ($logs as $log) {
            if (Gate::allows('delete', $log)) {
                $log->delete();
                $deletedCount++;
            }
        }

        return back()->with('success', $deletedCount . ' logs deleted.');
    }
}
