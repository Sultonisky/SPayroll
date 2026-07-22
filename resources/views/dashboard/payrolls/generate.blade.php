@extends('layouts.app')
@section('title', 'Run Payroll')

@section('contents')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-play-circle me-2"></i>Run Generate Mass Payroll
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        This will generate <strong>draft payroll records</strong> for all
                        <strong>active employees</strong> for the selected period.<br>
                        <span class="mt-3 d-block text-black">
                            Formula: <span class="fw-bold">Total Salary = Base Salary + Approved Bonuses</span><br>
                            Employees that already have a payroll record for this period will be skipped.
                        </span>
                    </div>

                    <form action="{{ route('payrolls.generate.bulk') }}" method="POST" id="runPayrollForm">
                        @csrf
                        <div class="row g-4">

                            {{-- Year --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-calendar"></i></span>
                                    <input type="number" name="year" id="inputYear"
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
                                    <select name="month" id="inputMonth" class="form-select border-start-0 @error('month') is-invalid @enderror" required>
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

                        {{-- Preview Table --}}
                        <div id="previewSection" class="mt-4" style="display:none;">
                            <hr>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-list-alt me-2 text-primary"></i>
                                    Payroll Preview - <span id="previewPeriodLabel"></span>
                                </h6>
                                <div class="d-flex gap-3 small text-muted" id="previewSummary"></div>
                            </div>

                            <div id="periodWarning"></div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle table-sm">
                                    <thead class="table-light text-dark small text-uppercase">
                                        <tr>
                                            <th width="5%" class="text-center">No.</th>
                                            <th>Employee</th>
                                            <th>Position</th>
                                            <th>Type</th>
                                            <th class="text-end">Base Salary</th>
                                            <th class="text-end">Bonus</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="previewTableBody"></tbody>
                                    <tfoot id="previewTableFoot" class="fw-bold table-dark"></tfoot>
                                </table>
                            </div>

                            <div id="previewLoading" class="text-center py-4" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                <span class="text-muted">Loading preview...</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 pt-4 border-top">
                            <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="button" id="btnRunPayroll"
                                class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black"
                                data-coreui-toggle="modal"
                                data-coreui-target="#runPayrollModal"
                                disabled>
                                <i class="fas fa-play-circle me-2"></i>Run Payroll
                            </button>
                        </div>
                    </form>

                    {{-- Confirm Modal --}}
                    <x-modal id="runPayrollModal"
                        title="Run Payroll"
                        type="primary"
                        icon="fa-play-circle"
                        confirmText="Yes, Run Payroll">
                        <div class="py-2">
                            <i class="fas fa-play-circle text-primary fa-3x mb-3"></i>
                            <h5 class="fw-bold">Run Payroll?</h5>
                            <p class="text-muted mb-1" id="modalSummaryText">
                                This will generate <strong>draft payroll records</strong> for all active employees in the selected period.
                            </p>
                            <p class="text-muted small mb-0">Employees with an existing record for this period will be skipped.</p>
                        </div>
                        <x-slot:footer>
                            <button type="button"
                                class="btn bg-primary text-black rounded-pill px-4 shadow-sm fw-bold"
                                onclick="document.getElementById('runPayrollForm').submit()">
                                <i class="fas fa-play-circle me-2"></i>Yes, Run Payroll
                            </button>
                        </x-slot:footer>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const previewUrl   = '{{ route('payrolls.generate.preview') }}';
    const $year        = $('#inputYear');
    const $month       = $('#inputMonth');
    const $section     = $('#previewSection');
    const $loading     = $('#previewLoading');
    const $tbody       = $('#previewTableBody');
    const $tfoot       = $('#previewTableFoot');
    const $summary     = $('#previewSummary');
    const $periodLabel = $('#previewPeriodLabel');
    const $btnRun      = $('#btnRunPayroll');

    let debounceTimer = null;

    function formatRp(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }

    function fetchPreview() {
        const year  = $year.val();
        const month = $month.val();

        if (!year || !month) return;

        $section.show();
        $loading.show();
        $tbody.empty();
        $tfoot.empty();
        $btnRun.prop('disabled', true);

        $.get(previewUrl, { year, month })
            .done(function (data) {
                $loading.hide();

                const monthNames = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
                $periodLabel.text(monthNames[parseInt(month)] + ' ' + year);

                // Warn if selected period is in the past
                const now = new Date();
                const selectedDate = new Date(year, parseInt(month) - 1);
                const currentMonth = new Date(now.getFullYear(), now.getMonth());
                let periodWarning = '';
                if (selectedDate < currentMonth) {
                    periodWarning = `<div class="alert alert-warning py-2 px-3 mb-3 small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Past period selected.</strong> You are generating payroll for a past period (${monthNames[parseInt(month)]} ${year}). Make sure this is intentional (e.g. missed payroll or new employee retroactive).
                    </div>`;
                } else if (selectedDate > currentMonth) {
                    periodWarning = `<div class="alert alert-info py-2 px-3 mb-3 small">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Future period selected.</strong> You are generating payroll for a future period (${monthNames[parseInt(month)]} ${year}).
                    </div>`;
                }
                $('#periodWarning').html(periodWarning);

                let toGenerate = 0, skipped = 0;
                let totalBase = 0, totalBonus = 0, totalSalary = 0;

                let rows = '';
                data.forEach(function (item, idx) {
                    const isDone = item.already_done;
                    if (isDone) skipped++; else toGenerate++;

                    totalBase   += isDone ? 0 : item.base_salary;
                    totalBonus  += isDone ? 0 : item.bonus;
                    totalSalary += isDone ? 0 : item.total_salary;

                    const rowClass = isDone ? 'text-muted' : '';
                    const badge    = isDone
                        ? '<span class="badge bg-secondary rounded-pill px-3">Skip (exists)</span>'
                        : '<span class="badge bg-success rounded-pill px-3">Will Generate</span>';

                    const typeBadge = item.employee.employee_type === 'fulltime'
                        ? '<span class="badge bg-primary text-black rounded-pill px-2 small">Fulltime</span>'
                        : '<span class="badge bg-warning text-black rounded-pill px-2 small">Internship</span>';

                    const bonusCell = item.bonus > 0
                        ? `<span class="text-success fw-semibold">+ ${formatRp(item.bonus)}</span>`
                        : '<span class="text-muted">—</span>';

                    rows += `
                        <tr class="${rowClass}">
                            <td class="text-center">${idx + 1}</td>
                            <td>
                                <div class="fw-bold">${item.employee.name}</div>
                                <small class="text-muted font-monospace">${item.employee.employee_code ?? ''}</small>
                            </td>
                            <td class="small">${item.employee.position?.name ?? '—'}</td>
                            <td>${typeBadge}</td>
                            <td class="text-end">${isDone ? '—' : formatRp(item.base_salary)}</td>
                            <td class="text-end">${isDone ? '—' : bonusCell}</td>
                            <td class="text-end fw-bold">${isDone ? '—' : formatRp(item.total_salary)}</td>
                            <td class="text-center">${badge}</td>
                        </tr>`;
                });

                $tbody.html(rows);

                // Footer totals
                $tfoot.html(`
                    <tr>
                        <td colspan="4" class="text-end small text-muted">Total (new records only)</td>
                        <td class="text-end">${formatRp(totalBase)}</td>
                        <td class="text-end text-success">+ ${formatRp(totalBonus)}</td>
                        <td class="text-end">${formatRp(totalSalary)}</td>
                        <td></td>
                    </tr>
                `);

                // Summary badges
                $summary.html(`
                    <span class="badge bg-primary text-black rounded-pill px-3">${toGenerate} will be created</span>
                    <span class="badge bg-secondary rounded-pill px-3">${skipped} skipped</span>
                `);

                // Update modal text
                $('#modalSummaryText').html(
                    `Will create <strong>${toGenerate} draft payroll records</strong> for ${monthNames[parseInt(month)]} ${year}. ${skipped > 0 ? skipped + ' employees will be skipped (already have a record).' : ''}`
                );

                // Only enable Run if there's something to generate
                $btnRun.prop('disabled', toGenerate === 0);
            })
            .fail(function () {
                $loading.hide();
                $tbody.html('<tr><td colspan="8" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load preview.</td></tr>');
            });
    }

    // Trigger on change with debounce for year input
    $year.on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchPreview, 500);
    });

    $month.on('change', fetchPreview);

    // Load preview on page load with default values
    fetchPreview();
});
</script>
@endpush
