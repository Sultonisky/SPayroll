@extends('layouts.app')
@section('title', 'Request Adjustment')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-edit me-2"></i>Request Attendance Adjustment
                    </h5>
                    <a href="{{ route('attendance-records.show', $attendanceRecord->id) }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('attendance-adjustments.store', $attendanceRecord->id) }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card border-0 bg-body-tertiary shadow-sm mb-4">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-primary mb-3">Current Attendance Data</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="fw-bold">Employee:</div>
                                                <div>{{ $attendanceRecord->employee->name }} ({{ $attendanceRecord->employee->nik }})</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fw-bold">Date:</div>
                                                <div>{{ $attendanceRecord->attendance_date->translatedFormat('d F Y') }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="fw-bold">Check In / Out:</div>
                                                <div>{{ $attendanceRecord->check_in }} - {{ $attendanceRecord->check_out }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">New Check In</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-clock"></i></span>
                                    <input type="time" name="new_check_in" value="{{ old('new_check_in', $attendanceRecord->check_in) }}" class="form-control border-start-0 @error('new_check_in') is-invalid @enderror">
                                </div>
                                @error('new_check_in')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">New Check Out</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-clock"></i></span>
                                    <input type="time" name="new_check_out" value="{{ old('new_check_out', $attendanceRecord->check_out) }}" class="form-control border-start-0 @error('new_check_out') is-invalid @enderror">
                                </div>
                                @error('new_check_out')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-align-left"></i></span>
                                    <textarea name="reason" class="form-control border-start-0 @error('reason') is-invalid @enderror" placeholder="Enter reason for adjustment" required>{{ old('reason') }}</textarea>
                                </div>
                                @error('reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-save me-2"></i>Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
