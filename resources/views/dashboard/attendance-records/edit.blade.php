@extends('layouts.app')
@section('title', 'Edit Attendance Record')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-edit me-2"></i>Edit Attendance Record
                    </h5>
                    <a href="{{ route('attendance-records.show', $attendanceRecord->id) }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('attendance-records.update', $attendanceRecord->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Check In</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-clock"></i></span>
                                    <input type="time" name="check_in" value="{{ old('check_in', $attendanceRecord->check_in) }}" class="form-control border-start-0 @error('check_in') is-invalid @enderror">
                                </div>
                                @error('check_in')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Check Out</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-clock"></i></span>
                                    <input type="time" name="check_out" value="{{ old('check_out', $attendanceRecord->check_out) }}" class="form-control border-start-0 @error('check_out') is-invalid @enderror">
                                </div>
                                @error('check_out')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Attendance Status</label>
                                <select name="attendance_status" class="form-select @error('attendance_status') is-invalid @enderror">
                                    <option value="present" {{ $attendanceRecord->attendance_status == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="late" {{ $attendanceRecord->attendance_status == 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="need_review" {{ $attendanceRecord->attendance_status == 'need_review' ? 'selected' : '' }}>Need Review</option>
                                    <option value="sick" {{ $attendanceRecord->attendance_status == 'sick' ? 'selected' : '' }}>Sick</option>
                                    <option value="leave" {{ $attendanceRecord->attendance_status == 'leave' ? 'selected' : '' }}>Leave</option>
                                    <option value="absent" {{ $attendanceRecord->attendance_status == 'absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                                @error('attendance_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Notes</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-align-left"></i></span>
                                    <textarea name="notes" class="form-control border-start-0 @error('notes') is-invalid @enderror" placeholder="Enter notes">{{ old('notes', $attendanceRecord->notes) }}</textarea>
                                </div>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
