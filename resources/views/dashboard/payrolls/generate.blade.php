@extends('layouts.app')
@section('title', 'Run Payroll')

@section('contents')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-play-circle me-2"></i>Run Payroll
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-secondary mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        This will generate <strong>draft payroll records</strong> for all
                        <strong>active employees</strong> for the selected period.<br>
                        <span class="mt-3 d-block text-black">
                            Formula: <span  class="fw-bold">Total Salary = Base Salary + Approved Bonuses</span><br>
                            Employees that already have a payroll record for this period will be skipped.
                        </span>
                    </div>

                    <form action="{{ route('payrolls.generate.bulk') }}" method="POST">
                        @csrf
                        <div class="row g-4">

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
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('month') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Pay Date --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Pay Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-calendar-check"></i></span>
                                    <input type="date" name="pay_date"
                                        class="form-control border-start-0 @error('pay_date') is-invalid @enderror"
                                        value="{{ old('pay_date', now()->format('Y-m-25')) }}" required>
                                </div>
                                @error('pay_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit"
                                class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold"
                                onclick="return confirm('Run payroll for all active employees in this period?')">
                                <i class="fas fa-play-circle me-2"></i>Run Payroll
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
