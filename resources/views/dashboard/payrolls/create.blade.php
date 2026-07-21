@extends('layouts.app')
@section('title', 'Add Payroll')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-plus-circle me-2"></i>Add Payroll Record
                    </h5>
                    <a href="{{ route('payrolls.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        For bulk generation, use <a href="{{ route('payrolls.generate') }}" class="alert-link">Run Payroll</a>.
                        This form is for manual single-record entry.
                    </div>

                    <form action="{{ route('payrolls.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">

                            {{-- Employee --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Employee <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-user"></i></span>
                                    <select name="employee_id" id="employee_id"
                                        class="form-select border-start-0 @error('employee_id') is-invalid @enderror" required>
                                        <option value="">Select employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                data-base-fulltime="{{ $employee->position?->base_salary_fulltime ?? 0 }}"
                                                data-base-internship="{{ $employee->position?->base_salary_internship ?? 0 }}"
                                                data-type="{{ $employee->employee_type }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                                @if ($employee->employee_code) ({{ $employee->employee_code }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Base Salary (auto-filled, editable) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Base Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="base_salary" id="base_salary" step="1000"
                                        class="form-control border-start-0 @error('base_salary') is-invalid @enderror"
                                        value="{{ old('base_salary', 0) }}" min="0" required>
                                </div>
                                @error('base_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                                        value="{{ old('pay_date', now()->format('Y-m-d')) }}" required>
                                </div>
                                @error('pay_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Bonus --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-gift"></i></span>
                                    <input type="number" name="bonus" id="bonus" step="1000"
                                        class="form-control border-start-0 @error('bonus') is-invalid @enderror"
                                        value="{{ old('bonus', 0) }}" min="0" oninput="recalcTotal()">
                                </div>
                                @error('bonus') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Total Salary (calculated) --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-coins"></i></span>
                                    <input type="number" name="total_salary" id="total_salary" step="1000"
                                        class="form-control border-start-0 bg-light @error('total_salary') is-invalid @enderror"
                                        value="{{ old('total_salary', 0) }}" min="0" readonly>
                                </div>
                                <small class="text-muted">Auto-calculated: Base Salary + Bonus</small>
                                @error('total_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-toggle-on"></i></span>
                                    <select name="status" class="form-select border-start-0 @error('status') is-invalid @enderror" required>
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                </div>
                                @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Notes</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-sticky-note"></i></span>
                                    <textarea name="notes" rows="2"
                                        class="form-control border-start-0 @error('notes') is-invalid @enderror"
                                        placeholder="Optional notes...">{{ old('notes') }}</textarea>
                                </div>
                                @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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

@push('scripts')
<script>
    function recalcTotal() {
        const base  = parseFloat(document.getElementById('base_salary').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus').value) || 0;
        document.getElementById('total_salary').value = base + bonus;
    }

    document.getElementById('employee_id').addEventListener('change', function () {
        const opt  = this.options[this.selectedIndex];
        const type = opt.getAttribute('data-type');
        let salary = 0;
        if (type === 'fulltime')   salary = parseFloat(opt.getAttribute('data-base-fulltime'))   || 0;
        if (type === 'internship') salary = parseFloat(opt.getAttribute('data-base-internship')) || 0;
        document.getElementById('base_salary').value = salary;
        recalcTotal();
    });

    document.getElementById('base_salary').addEventListener('input', recalcTotal);
</script>
@endpush
