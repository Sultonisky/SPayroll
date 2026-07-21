@extends('layouts.app')
@section('title', 'Bonus Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-gift me-2"></i>Bonus Detail
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($bonus->isPending() && (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager'])))
                            <a href="{{ route('bonuses.edit', $bonus->id) }}"
                                class="btn btn-warning btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                        @endif
                        <a href="{{ route('bonuses.index') }}"
                            class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Status banner --}}
                    @php
                        $bannerClass = match($bonus->status) {
                            'approved' => 'alert-success',
                            'rejected' => 'alert-danger',
                            default    => 'alert-warning',
                        };
                    @endphp
                    <div class="alert {{ $bannerClass }} d-flex align-items-center mb-4">
                        <i class="fas fa-circle me-2"></i>
                        <strong>{{ ucfirst($bonus->status) }}</strong>
                        @if ($bonus->approved_at)
                            &nbsp;— {{ $bonus->approved_at->translatedFormat('d F Y, H:i') }} WIB
                            by {{ $bonus->approvedBy?->name ?? '-' }}
                        @endif
                        @if ($bonus->notes && $bonus->isRejected())
                            <span class="ms-2 text-muted">| {{ $bonus->notes }}</span>
                        @endif
                    </div>

                    {{-- Approve / Reject actions --}}
                    @if ($bonus->isPending() && (auth()->user()->isAdmin() || auth()->user()->role === 'HR'))
                        <div class="d-flex gap-2 mb-4">
                            <form action="{{ route('bonuses.approve', $bonus->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-4"
                                    onclick="return confirm('Approve this bonus?')">
                                    <i class="fas fa-check me-2"></i>Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-4"
                                data-coreui-toggle="modal" data-coreui-target="#rejectModal">
                                <i class="fas fa-times me-2"></i>Reject
                            </button>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Employee</div>
                                    <div class="fs-6 fw-bold text-body">{{ $bonus->employee?->name ?? '-' }}</div>
                                    <small class="text-muted font-monospace">{{ $bonus->employee?->employee_code ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Period</div>
                                    <div class="fs-6 fw-bold text-body">
                                        {{ \Carbon\Carbon::create($bonus->year, $bonus->month)->translatedFormat('F Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Bonus Type</div>
                                    <div class="fs-6 fw-bold text-body">{{ $bonus->type }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Amount</div>
                                    <div class="fs-5 fw-bold text-success">
                                        Rp {{ number_format($bonus->amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Submitted At</div>
                                    <div class="fs-6 fw-bold text-body">{{ $bonus->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Approved By</div>
                                    <div class="fs-6 fw-bold text-body">{{ $bonus->approvedBy?->name ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        @if ($bonus->description)
                            <div class="col-12">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Description</div>
                                        <div class="fs-6 text-body">{{ $bonus->description }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($bonus->notes)
                            <div class="col-12">
                                <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Reviewer Notes</div>
                                        <div class="fs-6 text-body">{{ $bonus->notes }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    @if ($bonus->isPending())
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('bonuses.reject', $bonus->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reject Bonus</h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label fw-bold">Reason (optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Enter reason for rejection..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-coreui-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm">Confirm Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
