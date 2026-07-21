@extends('layouts.app')
@section('title', 'Edit Payroll')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-edit me-2"></i>Edit Payroll
                    </h5>
                    <a href="{{ route('payrolls.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    {{-- Employee & Period (read-only once created) --}}
                    <div class="alert alert-secondary mb-4">
                        <i class="fas fa-lock me-2"></i>
                        Employee and period cannot be changed after creation.
                        <strong>{{ $payroll->employee?->name }}</strong> —
                        {{ \Carbon\Carbon::create($payroll->year, $payroll->month)->translatedFormat('F Y') }}
                    </div>

                    <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">

                            {{-- Base Salary --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Base Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="base_salary" id="base_salary" step="1000"
                                        class="form-control border-start-0 @error('base_salary') is-invalid @enderror"
                                        value="{{ old('base_salary', $payroll->base_salary) }}" min="0"
                                        required oninput="recalcTotal()">
                                </div>
                                @error('base_salary') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Bonus --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-gift"></i></span>
                                    <input type="number" name="bonus" id="bonus" step="1000"
                                        class="form-control border-start-0 @error('bonus') is-invalid @enderror"
                                        value="{{ old('bonus', $payroll->bonus) }}" min="0"
                                        oninput="recalcTotal()">
                                </div>
                                @error('bonus') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Total Salary --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-coins"></i></span>
                                    <input type="number" name="total_salary" id="total_salary" step="1000"
                                        class="form-control border-start-0 bg-light @error('total_salary') is-invalid @enderror"
                                        value="{{ old('total_salary', $payroll->total_salary) }}" min="0" readonly>
                                </div>
                                <small class="text-muted">Auto-calculated: Base Salary + Bonus</small>
                            </div>

                            {{-- Pay Date --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pay Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-calendar-check"></i></span>
                                    <input type="date" name="pay_date"
                                        class="form-control border-start-0 @error('pay_date') is-invalid @enderror"
                                        value="{{ old('pay_date', $payroll->pay_date?->format('Y-m-d')) }}" required>
                                </div>
                                @error('pay_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-toggle-on"></i></span>
                                    <select name="status" class="form-select border-start-0 @error('status') is-invalid @enderror" required>
                                        <option value="draft"    {{ old('status', $payroll->status) === 'draft'    ? 'selected' : '' }}>Draft</option>
                                        <option value="approved" {{ old('status', $payroll->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="paid"     {{ old('status', $payroll->status) === 'paid'     ? 'selected' : '' }}>Paid</option>
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
                                        placeholder="Optional notes...">{{ old('notes', $payroll->notes) }}</textarea>
                                </div>
                                @error('notes') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-check-circle me-2"></i>Update Payroll
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
</script>
@endpush
