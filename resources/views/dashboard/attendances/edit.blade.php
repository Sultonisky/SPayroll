@extends('layouts.app')
@section('title', 'Edit Attendance')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-edit me-2"></i>Edit Attendance
                    </h5>
                    <a href="{{ route('attendances.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Employee --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-user"></i></span>
                                    <select name="employee_id"
                                        class="form-select border-start-0 @error('employee_id') is-invalid @enderror"
                                        required>
                                        <option value="" disabled>Select employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id', $attendance->employee_id) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Year --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-calendar"></i></span>
                                    <input type="number" name="year" min="2000" max="2100"
                                        class="form-control border-start-0 @error('year') is-invalid @enderror"
                                        placeholder="Enter year" value="{{ old('year', $attendance->year) }}" required>
                                </div>
                                @error('year')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Month --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Month <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-calendar-alt"></i></span>
                                    <input type="number" name="month" min="1" max="12"
                                        class="form-control border-start-0 @error('month') is-invalid @enderror"
                                        placeholder="Enter month (1-12)"
                                        value="{{ old('month', $attendance->month) }}" required>
                                </div>
                                @error('month')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Work Days --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Work Days <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-business-time"></i></span>
                                    <input type="number" name="work_days" min="0"
                                        class="form-control border-start-0 @error('work_days') is-invalid @enderror"
                                        placeholder="Enter work days"
                                        value="{{ old('work_days', $attendance->work_days) }}" required>
                                </div>
                                @error('work_days')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Present --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Present <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-check"></i></span>
                                    <input type="number" name="present" min="0"
                                        class="form-control border-start-0 @error('present') is-invalid @enderror"
                                        placeholder="Enter present days"
                                        value="{{ old('present', $attendance->present) }}" required>
                                </div>
                                @error('present')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Sick --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sick <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-thermometer-half"></i></span>
                                    <input type="number" name="sick" min="0"
                                        class="form-control border-start-0 @error('sick') is-invalid @enderror"
                                        placeholder="Enter sick days" value="{{ old('sick', $attendance->sick) }}"
                                        required>
                                </div>
                                @error('sick')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Leave --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Leave <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-umbrella-beach"></i></span>
                                    <input type="number" name="leave" min="0"
                                        class="form-control border-start-0 @error('leave') is-invalid @enderror"
                                        placeholder="Enter leave days" value="{{ old('leave', $attendance->leave) }}"
                                        required>
                                </div>
                                @error('leave')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Alpha --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Alpha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-times"></i></span>
                                    <input type="number" name="alpha" min="0"
                                        class="form-control border-start-0 @error('alpha') is-invalid @enderror"
                                        placeholder="Enter alpha days" value="{{ old('alpha', $attendance->alpha) }}"
                                        required>
                                </div>
                                @error('alpha')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Overtime Hours --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Overtime Hours <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-clock"></i></span>
                                    <input type="number" name="overtime_hours" step="0.01" min="0"
                                        class="form-control border-start-0 @error('overtime_hours') is-invalid @enderror"
                                        placeholder="Enter overtime hours"
                                        value="{{ old('overtime_hours', $attendance->overtime_hours) }}" required>
                                </div>
                                @error('overtime_hours')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Notes</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-align-left"></i></span>
                                    <textarea name="notes"
                                        class="form-control border-start-0 @error('notes') is-invalid @enderror"
                                        placeholder="Enter notes">{{ old('notes', $attendance->notes) }}</textarea>
                                </div>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset"
                                class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit"
                                class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-check-circle me-2"></i>Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
