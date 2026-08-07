@extends('layouts.app')
@section('title', 'Audit Log')

@section('contents')
    <div class="row">
        <div class="col-12">

            {{-- Page Header --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-shield-alt me-2"></i>Audit Log
                    </h5>
                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                        {{ number_format($logs->total()) }} entries
                    </span>
                </div>

                {{-- Filters --}}
<div class="card-body border-bottom">
    <form method="GET" action="{{ route('audit-logs.index') }}" id="filter-form">
        <div class="row g-3 mb-3">

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">User</label>
                <select name="user_id" class="form-select form-select-sm rounded-pill shadow-sm">
                    <option value="">All Users</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Action</label>
                <select name="action" class="form-select form-select-sm rounded-pill shadow-sm">
                    <option value="">All Actions</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $act)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Model</label>
                <select name="auditable_type" class="form-select form-select-sm rounded-pill shadow-sm">
                    <option value="">All Models</option>
                    @foreach ($modelTypes as $type)
                        <option value="{{ $type }}" {{ request('auditable_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">IP Address</label>
                <input
                    type="text"
                    name="ip_address"
                    class="form-control form-control-sm rounded-pill shadow-sm"
                    placeholder="e.g. 192.168.1.1"
                    value="{{ request('ip_address') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">From</label>
                <input
                    type="date"
                    name="date_from"
                    class="form-control form-control-sm rounded-pill shadow-sm"
                    value="{{ request('date_from') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">To</label>
                <input
                    type="date"
                    name="date_to"
                    class="form-control form-control-sm rounded-pill shadow-sm"
                    value="{{ request('date_to') }}">
            </div>

            <div class="d-flex align-items-end justify-content-end gap-2">
                <a href="{{ route('audit-logs.index') }}"
                    class="btn btn-outline-secondary btn-sm rounded-pill px-4 shadow-sm">
                    <i class="fas fa-undo me-2"></i>Reset
                </a>

                <button type="submit"
                    class="btn btn-info text-white btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-search me-2"></i>Search
                </button>
            </div>

        </div>
    </form>
</div>


                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-3" width="15%">Timestamp</th>
                                    <th width="15%">User</th>
                                    <th width="10%">Action</th>
                                    <th width="10%">Model</th>
                                    <th>Description</th>
                                    <th width="13%">IP Address</th>
                                    <th width="5%" class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td class="ps-3 text-muted small">
                                            {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }}
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $log->created_at->diffForHumans() }}
                                            </div>
                                        </td>

                                        <td>
                                            @if ($log->user)
                                                <div class="fw-semibold small">{{ $log->user->name }}</div>
                                                <div class="text-muted" style="font-size: 0.72rem;">{{ $log->user->role }}</div>
                                            @else
                                                <span class="text-muted fst-italic small">System</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-{{ $log->action_color }} rounded-pill px-2 py-1 small">
                                                <i class="fas {{ $log->action_icon }} me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>

                                        <td class="small">
                                            @if ($log->auditable_type)
                                                <span class="fw-semibold">{{ $log->auditable_name }}</span>
                                                @if ($log->auditable_id)
                                                    <span class="text-muted"> #{{ $log->auditable_id }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td class="small text-truncate" style="max-width: 250px;" title="{{ $log->description }}">
                                            {{ $log->description ?? '—' }}
                                        </td>

                                        <td>
                                            <code class="small text-dark bg-light px-2 py-1 rounded">
                                                {{ $log->ip_address ?? '—' }}
                                            </code>
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('audit-logs.show', $log->id) }}"
                                                class="btn btn-white btn-sm border shadow-sm px-2" title="View Detail">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 d-block opacity-25"></i>
                                            No audit log entries found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($logs->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center py-2">
                        <span class="small text-muted">
                            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries
                        </span>
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
