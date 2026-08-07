@extends('layouts.app')
@section('title', 'Audit Log Detail')

@section('contents')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-shield-alt me-2"></i>Audit Log Detail
                    </h5>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body">

                    {{-- Action badge --}}
                    <div class="text-center py-3 mb-4">
                        <span class="badge bg-{{ $auditLog->action_color }} px-4 py-2 fs-6 rounded-pill">
                            <i class="fas {{ $auditLog->action_icon }} me-2"></i>
                            {{ ucfirst(str_replace('_', ' ', $auditLog->action)) }}
                        </span>
                        <div class="text-muted small mt-2">
                            {{ $auditLog->created_at->setTimezone('Asia/Jakarta')->format('l, d F Y — H:i:s T') }}
                        </div>
                    </div>

                    <div class="row g-3">

                        {{-- Who --}}
                        <div class="col-12 col-md-6">
                            <div class="card border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted small text-uppercase fw-bold mb-3">
                                        <i class="fas fa-user me-2"></i>Performed By
                                    </h6>
                                    @if ($auditLog->user)
                                        <div class="fw-bold">{{ $auditLog->user->name }}</div>
                                        <div class="text-muted small">{{ $auditLog->user->email }}</div>
                                        <span class="badge bg-danger rounded-pill mt-1">{{ $auditLog->user->role }}</span>
                                    @else
                                        <span class="text-muted fst-italic">System / Unauthenticated</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Network Context --}}
                        <div class="col-12 col-md-6">
                            <div class="card border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted small text-uppercase fw-bold mb-3">
                                        <i class="fas fa-network-wired me-2"></i>Network Context
                                    </h6>
                                    <div class="mb-2">
                                        <span class="text-muted small">IP Address</span>
                                        <div>
                                            <code class="border px-2 py-1 rounded small">
                                                {{ $auditLog->ip_address ?? '—' }}
                                            </code>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-muted small">User Agent</span>
                                        <div class="small text-break" style="word-break: break-word;">
                                            {{ $auditLog->user_agent ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Target --}}
                        <div class="col-12 col-md-6">
                            <div class="card border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted small text-uppercase fw-bold mb-3">
                                        <i class="fas fa-database me-2"></i>Affected Record
                                    </h6>
                                    @if ($auditLog->auditable_type)
                                        <div class="mb-1">
                                            <span class="text-muted small">Model</span>
                                            <div class="fw-semibold">{{ $auditLog->auditable_name }}</div>
                                        </div>
                                        <div class="mb-1">
                                            <span class="text-muted small">ID</span>
                                            <div><code>#{{ $auditLog->auditable_id ?? '—' }}</code></div>
                                        </div>
                                        <div class="text-muted small">{{ $auditLog->auditable_type }}</div>
                                    @else
                                        <span class="text-muted fst-italic">No specific record</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Request --}}
                        <div class="col-12 col-md-6">
                            <div class="card border-0 h-100">
                                <div class="card-body">
                                    <h6 class="text-muted small text-uppercase fw-bold mb-3">
                                        <i class="fas fa-link me-2"></i>HTTP Request
                                    </h6>
                                    @if ($auditLog->url)
                                        <div class="mb-1">
                                            <span class="text-muted small">Method</span>
                                            <div>
                                                <span class="badge bg-primary">{{ $auditLog->method ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-muted small">URL</span>
                                            <div class="small text-break">{{ $auditLog->url }}</div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if ($auditLog->description)
                            <div class="col-12">
                                <div class="card border-0">
                                    <div class="card-body">
                                        <h6 class="text-muted small text-uppercase fw-bold mb-2">
                                            <i class="fas fa-info-circle me-2"></i>Description
                                        </h6>
                                        <p class="mb-0">{{ $auditLog->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Old Values --}}
                        @if ($auditLog->old_values)
                            <div class="col-12 col-md-6">
                                <div class="card border-0">
                                    <div class="card-body">
                                        <h6 class="text-muted small text-uppercase fw-bold mb-3">
                                            <i class="fas fa-history me-2 text-danger"></i>Before (Old Values)
                                        </h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            @foreach ($auditLog->old_values as $field => $value)
                                                <tr>
                                                    <td class="text-muted small pe-3 text-nowrap" width="40%">
                                                        {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                    </td>
                                                    <td class="small text-break">
                                                        @if (is_null($value))
                                                            <span class="text-muted fst-italic">null</span>
                                                        @elseif (is_bool($value))
                                                            <span class="badge bg-{{ $value ? 'success' : 'secondary' }}">{{ $value ? 'true' : 'false' }}</span>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- New Values --}}
                        @if ($auditLog->new_values)
                            <div class="col-12 col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted small text-uppercase fw-bold mb-3">
                                            <i class="fas fa-check-circle me-2 text-success"></i>After (New Values)
                                        </h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            @foreach ($auditLog->new_values as $field => $value)
                                                <tr>
                                                    <td class="text-muted small pe-3 text-nowrap" width="40%">
                                                        {{ ucfirst(str_replace('_', ' ', $field)) }}
                                                    </td>
                                                    <td class="small text-break">
                                                        @if (is_null($value))
                                                            <span class="text-muted fst-italic">null</span>
                                                        @elseif (is_bool($value))
                                                            <span class="badge bg-{{ $value ? 'success' : 'secondary' }}">{{ $value ? 'true' : 'false' }}</span>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>{{-- /.row --}}
                </div>
            </div>

        </div>
    </div>
@endsection
