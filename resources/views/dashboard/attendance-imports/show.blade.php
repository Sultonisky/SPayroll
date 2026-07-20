@extends('layouts.app')
@section('title', 'Import Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-file-alt me-2"></i>Import Detail
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendance-imports.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
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
                                    <i class="fas fa-file-import fa-5x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md ms-md-4 text-center text-md-start">
                            <h2 class="fw-bold text-body mb-1">{{ $attendanceImport->file_name }}</h2>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-calendar-check me-2 text-info"></i>Imported: {{ $attendanceImport->imported_at?->translatedFormat('d M Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">File Name</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceImport->file_name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Imported By</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceImport->importedBy->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Status</div>
                                    <div class="fs-5 fw-bold text-body">
                                        @if($attendanceImport->status == 'pending')
                                            <span class="badge bg-warning rounded-pill">Pending</span>
                                        @elseif($attendanceImport->status == 'completed')
                                            <span class="badge bg-success rounded-pill">Completed</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill">Failed</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Total Rows</div>
                                    <div class="fs-5 fw-bold text-body">{{ $attendanceImport->total_rows }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-success mb-2">Success</div>
                                    <div class="fs-5 fw-bold text-success">{{ $attendanceImport->success_rows }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-danger mb-2">Failed</div>
                                    <div class="fs-5 fw-bold text-danger">{{ $attendanceImport->failed_rows }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($attendanceImport->attendanceRecords->count() > 0)
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="fw-bold text-primary mb-3">Imported Attendance Records</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light text-dark small text-uppercase">
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th>Employee</th>
                                            <th>Date</th>
                                            <th class="text-center">Check In</th>
                                            <th class="text-center">Check Out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendanceImport->attendanceRecords as $record)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                <td>{{ $record->employee->name }} ({{ $record->employee->nik }})</td>
                                                <td>{{ $record->attendance_date->translatedFormat('d M Y') }}</td>
                                                <td class="text-center">{{ $record->check_in }}</td>
                                                <td class="text-center">{{ $record->check_out }}</td>
                                                <td>
                                                    @if($record->attendance_status == 'present')
                                                        <span class="badge bg-success rounded-pill">Present</span>
                                                    @elseif($record->attendance_status == 'late')
                                                        <span class="badge bg-warning rounded-pill">Late</span>
                                                    @elseif($record->attendance_status == 'need_review')
                                                        <span class="badge bg-info rounded-pill">Need Review</span>
                                                    @else
                                                        <span class="badge bg-secondary rounded-pill">{{ $record->attendance_status }}</span>
                                                    @endif
                                                </td>
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
