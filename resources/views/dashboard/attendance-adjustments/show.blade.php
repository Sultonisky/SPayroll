@extends('layouts.app')
@section('title', 'Adjustment Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-file-alt me-2"></i>Adjustment Detail
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                            @if($attendanceAdjustment->status == 'pending')
                                <form action="{{ route('attendance-adjustments.approve', $attendanceAdjustment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                        <i class="fas fa-check-circle me-2"></i>Approve
                                    </button>
                                </form>
                                <form action="{{ route('attendance-adjustments.reject', $attendanceAdjustment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                        <i class="fas fa-times-circle me-2"></i>Reject
                                    </button>
                                </form>
                            @endif
                        @endif
                        <a href="{{ route('attendance-adjustments.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-auto text-center mb-3 mb-md-0">
                            <div class="d-inline-block position-relative">
                                <div class="text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-primary" style="width: 140px; height: 140px;">
                                    <i class="fas fa-edit fa-5x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md ms-md-4 text-center text-md-start">
                            <h2 class="fw-bold text-body mb-1">{{ $attendanceAdjustment->attendanceRecord->employee->name }}</h2>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-calendar me-2 text-info"></i>{{ $attendanceAdjustment->attendanceRecord->attendance_date->translatedFormat('d F Y') }}
                                </span>
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    @if($attendanceAdjustment->status == 'pending')
                                        <i class="fas fa-clock text-warning"></i> Pending
                                    @elseif($attendanceAdjustment->status == 'approved')
                                        <i class="fas fa-check-circle text-success"></i> Approved
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> Rejected
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Old Check In</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceAdjustment->old_check_in }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-success mb-2">New Check In</div>
                                    <div class="fs-5 fw-bold text-success">{{ $attendanceAdjustment->new_check_in }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Old Check Out</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceAdjustment->old_check_out }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-success mb-2">New Check Out</div>
                                    <div class="fs-5 fw-bold text-success">{{ $attendanceAdjustment->new_check_out }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top">
                        <h6 class="fw-bold text-primary mb-2">Reason</h6>
                        <p class="text-body">{{ $attendanceAdjustment->reason }}</p>
                    </div>

                    @if($attendanceAdjustment->approvedBy)
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="fw-bold text-primary mb-2">Approved / Rejected By</h6>
                            <p class="text-body">{{ $attendanceAdjustment->approvedBy->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
