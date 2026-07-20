@extends('layouts.app')
@section('title', 'Attendance Record Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-calendar-check me-2"></i>Attendance Record Detail
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if(auth()->user()->isAdmin() || auth()->user()->role == 'HR' || auth()->user()->role == 'manager')
                            <a href="{{ route('attendance-adjustments.create', $attendanceRecord->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-edit me-2"></i>Request Adjustment
                            </a>
                        @endif
                        @if(auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                            <a href="{{ route('attendance-records.edit', $attendanceRecord->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-pencil-alt me-2"></i>Edit
                            </a>
                        @endif
                        <a href="{{ route('attendance-records.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-auto text-center mb-3 mb-md-0">
                            <div class="d-inline-block position-relative">
                                <div class="text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-primary"
                                    style="width: 140px; height: 140px;">
                                    <i class="fas fa-user-clock fa-5x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md ms-md-4 text-center text-md-start">
                            <h2 class="fw-bold text-body mb-1">{{ $attendanceRecord->employee->name }}</h2>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-id-card me-2 text-info"></i>{{ $attendanceRecord->employee->nik }}
                                </span>
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-calendar me-2 text-info"></i>{{ $attendanceRecord->attendance_date->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Check In</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceRecord->check_in }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Check Out</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceRecord->check_out }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Work Hours</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceRecord->work_hours }}h</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-warning mb-2">Late Minutes</div>
                                    <div class="fs-5 fw-bold text-warning">{{ $attendanceRecord->late_minutes }}m</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-success mb-2">Overtime Minutes</div>
                                    <div class="fs-5 fw-bold text-success">{{ $attendanceRecord->overtime_minutes }}m</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Status</div>
                                    <div class="fs-5 fw-bold text-body">
                                        @if($attendanceRecord->attendance_status == 'present')
                                            <span class="badge bg-success rounded-pill">Present</span>
                                        @elseif($attendanceRecord->attendance_status == 'late')
                                            <span class="badge bg-warning rounded-pill">Late</span>
                                        @elseif($attendanceRecord->attendance_status == 'need_review')
                                            <span class="badge bg-info rounded-pill">Need Review</span>
                                        @elseif($attendanceRecord->attendance_status == 'sick')
                                            <span class="badge bg-secondary rounded-pill">Sick</span>
                                        @elseif($attendanceRecord->attendance_status == 'leave')
                                            <span class="badge bg-primary rounded-pill">Leave</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill">{{ $attendanceRecord->attendance_status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($attendanceRecord->notes)
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="fw-bold text-primary mb-2">Notes</h6>
                            <p class="text-body">{{ $attendanceRecord->notes }}</p>
                        </div>
                    @endif

                    @if($attendanceRecord->attendanceAdjustments->count() > 0)
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="fw-bold text-primary mb-3">Adjustment History</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light text-dark small text-uppercase">
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th>Old Check In</th>
                                            <th>New Check In</th>
                                            <th>Old Check Out</th>
                                            <th>New Check Out</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Approved By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendanceRecord->attendanceAdjustments as $adjustment)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                <td>{{ $adjustment->old_check_in }}</td>
                                                <td>{{ $adjustment->new_check_in }}</td>
                                                <td>{{ $adjustment->old_check_out }}</td>
                                                <td>{{ $adjustment->new_check_out }}</td>
                                                <td>{{ $adjustment->reason }}</td>
                                                <td>
                                                    @if($adjustment->status == 'pending')
                                                        <span class="badge bg-warning rounded-pill">Pending</span>
                                                    @elseif($adjustment->status == 'approved')
                                                        <span class="badge bg-success rounded-pill">Approved</span>
                                                    @else
                                                        <span class="badge bg-danger rounded-pill">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>{{ $adjustment->approvedBy?->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
