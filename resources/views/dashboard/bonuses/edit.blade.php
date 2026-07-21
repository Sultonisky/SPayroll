@extends('layouts.app')
@section('title', 'Edit Bonus')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-edit me-2"></i>Edit Bonus
                    </h5>
                    <a href="{{ route('bonuses.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    @if (!$bonus->isPending())
                        <div class="alert alert-warning">
                            <i class="fas fa-lock me-2"></i>
                            This bonus has been <strong>{{ $bonus->status }}</strong> and can no longer be edited.
                        </div>
                    @endif

                    <form action="{{ route('bonuses.update', $bonus->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">

                            {{-- Employee --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-user"></i></span>
                                    <select name="employee_id" class="form-select border-start-0 @error('employee_id') is-invalid @enderror"
                                        required {{ !$bonus->isPending() ? 'disabled' : '' }}>
                                        <option value="">Select employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id', $bonus->employee_id) == $employee->id ? 'selected' : '' }}>
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
                                        value="{{ old('type', $bonus->type) }}"
                                        list="bonus-type-suggestions"
                                        required {{ !$bonus->isPending() ? 'disabled' : '' }}>
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
                                        value="{{ old('year', $bonus->year) }}" min="2000" max="2100"
                                        required {{ !$bonus->isPending() ? 'disabled' : '' }}>
                                </div>
                                @error('year') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Month --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Month <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-calendar-alt"></i></span>
                                    <select name="month" class="form-select border-start-0 @error('month') is-invalid @enderror"
                                        required {{ !$bonus->isPending() ? 'disabled' : '' }}>
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ old('month', $bonus->month) == $m ? 'selected' : '' }}>
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
                                        value="{{ old('amount', $bonus->amount) }}" min="1"
                                        required {{ !$bonus->isPending() ? 'disabled' : '' }}>
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
                                        {{ !$bonus->isPending() ? 'disabled' : '' }}>{{ old('description', $bonus->description) }}</textarea>
                                </div>
                                @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        @if ($bonus->isPending())
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                                <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                    <i class="fas fa-check-circle me-2"></i>Update Bonus
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
