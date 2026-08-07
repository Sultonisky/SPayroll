<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    /**
     * Display the audit log listing.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user')
            ->orderByDesc('created_at');

        // Filter: user
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter: action
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        // Filter: model type
        if ($type = $request->query('auditable_type')) {
            $query->where('auditable_type', 'like', '%'.$type.'%');
        }

        // Filter: IP address
        if ($ip = $request->query('ip_address')) {
            $query->where('ip_address', 'like', '%'.$ip.'%');
        }

        // Filter: date range
        if ($dateFrom = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Data for filter dropdowns
        $users = User::withTrashed()->orderBy('name')->get(['id', 'name', 'email']);

        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $modelTypes = AuditLog::select('auditable_type')
            ->whereNotNull('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->map(fn ($type) => class_basename($type))
            ->unique()
            ->sort()
            ->values();

        return view('dashboard.audit-logs.index', compact(
            'logs',
            'users',
            'actions',
            'modelTypes',
        ));
    }

    /**
     * Show a single audit log entry.
     */
    public function show(AuditLog $auditLog)
    {
        Gate::authorize('view', $auditLog);

        $auditLog->load('user');

        return view('dashboard.audit-logs.show', compact('auditLog'));
    }
}
