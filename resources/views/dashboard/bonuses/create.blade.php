@extends('layouts.app')
@section('title', 'Add Bonus')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-gift me-2"></i>Add New Bonus
                    </h5>
                    <a href="{{ route('bonuses.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('bonuses.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">

                            {{-- Employee --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-user"></i></span>
                                    <select name="employee_id" class="form-select border-start-0 @error('employee_id') is-invalid @enderror" required>
                                        <option value="">Select employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                                @if ($employee->employee_code) ({{ $employee->employee_code }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Bonus Type --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bonus Type <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-tag"></i></span>
                                    <input type="text" name="type"
                                        class="form-control border-start-0 @error('type') is-invalid @enderror"
                                        placeholder="e.g. Performance Bonus, Project Completion..."
                                        value="{{ old('type') }}" list="bonus-type-suggestions" required>
                                    <datalist id="bonus-type-suggestions">
                                        <option value="Performance Bonus">
                                        <option value="Project Completion Bonus">
                                        <option value="Referral Bonus">
                                        <option value="Annual Bonus">
                                        <option value="Retention Bonus">
                                        <option value="Special Achievement">
                                    </datalist>
                                </div>
                                @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Year --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-calendar"></i></span>
                                    <input type="number" name="year"
                                        class="form-control border-start-0 @error('year') is-invalid @enderror"
                                        value="{{ old('year', now()->year) }}" min="2000" max="2100" required>
                                </div>
                                @error('year') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Month --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Month <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-calendar-alt"></i></span>
                                    <select name="month" class="form-select border-start-0 @error('month') is-invalid @enderror" required>
                                        <option value="">Select month</option>
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('month') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Amount (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="amount" step="1000"
                                        class="form-control border-start-0 @error('amount') is-invalid @enderror"
                                        placeholder="e.g. 1000000" value="{{ old('amount') }}" min="1" required>
                                </div>
                                @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-align-left"></i></span>
                                    <textarea name="description" rows="3"
                                        class="form-control border-start-0 @error('description') is-invalid @enderror"
                                        placeholder="Optional: describe the reason for this bonus">{{ old('description') }}</textarea>
                                </div>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-save me-2"></i>Submit Bonus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
