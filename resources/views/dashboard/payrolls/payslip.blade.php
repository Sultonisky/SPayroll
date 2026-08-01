@extends('layouts.app')
@section('title', 'Payslip')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-file-invoice me-2"></i>Payslip
                    </h5>
                    @if ($canViewAll)
                        <span class="badge bg-info text-white rounded-pill px-3 py-2 fs-6">
                            {{ $payrolls->count() }} records found
                        </span>
                    @endif
                </div>

                {{-- Filter (hidden for staff since they only see their own) --}}
                @if ($canViewAll)
                    <div class="card-body pb-0">
                        <form method="GET" action="{{ route('payrolls.payslip') }}" id="filter-form">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Year</label>
                                    <select name="year" class="form-select form-select-sm rounded-pill shadow-sm">
                                        <option value="">All Years</option>
                                        @foreach (range(date('Y'), date('Y') - 3) as $y)
                                            <option value="{{ $y }}" {{ ($filterYear ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Month</label>
                                    <select name="month" class="form-select form-select-sm rounded-pill shadow-sm">
                                        <option value="">All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ ($filterMonth ?? '') == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Employee</label>
                                    <select name="employee_id" class="form-select form-select-sm rounded-pill shadow-sm">
                                        <option value="">All Employees</option>
                                        @foreach ($allEmployees as $emp)
                                            <option value="{{ $emp->id }}" {{ ($filterEmployeeId ?? '') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }} ({{ $emp->employee_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex align-items-end justify-content-end gap-2">
                                    <a href="{{ route('payrolls.payslip') }}"
                                        class="btn btn-outline-secondary btn-sm rounded-pill px-4 shadow-sm">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </a>
                                    <button type="submit"
                                        class="btn btn-info text-white btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="card-body">
                    @if ($payrolls->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice fa-3x mb-3 opacity-50"></i>
                            <p class="mb-0 fw-semibold">No payslip found.</p>
                            <p class="small mt-1">Payslips are available once payroll is marked as <span class="badge bg-success">Paid</span>.</p>
                        </div>
                    @else
                        <table class="table table-hover align-middle" id="payslipTable">
                            <thead class="table-light text-dark small text-uppercase">
                                <tr>
                                    <th width="5%" class="text-center">No.</th>
                                    <th>Employee</th>
                                    <th>Period</th>
                                    <th>Pay Date</th>
                                    <th>Base Salary</th>
                                    <th>Bonus</th>
                                    <th>Total Salary</th>
                                    <th width="12%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $payroll)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-body">{{ $payroll->employee?->name ?? '-' }}</div>
                                            <div class="small text-muted">{{ $payroll->employee?->employee_code ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border" data-order="{{ $payroll->year * 100 + $payroll->month }}">
                                                <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::create($payroll->year, $payroll->month)->translatedFormat('F Y') }}
                                            </span>
                                        </td>
                                        <td class="text-body">
                                            {{ $payroll->pay_date?->translatedFormat('d M Y') ?? '-' }}
                                        </td>
                                        <td class="text-body">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($payroll->bonus > 0)
                                                <span class="text-success fw-semibold">+ Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-success">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                <a href="{{ route('payrolls.payslip.show', $payroll->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="View Payslip">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>
                                                <a href="{{ route('payrolls.payslip.show', $payroll->id) }}?print=1"
                                                    target="_blank"
                                                    class="btn btn-white btn-sm px-3" title="Print Payslip">
                                                    <i class="fas fa-print text-secondary"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #payslipTable thead th { color: #000 !important; }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#payslipTable')) {
                $('#payslipTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[2, "desc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [7] },
                        { "type": "num", "targets": [2] }
                    ],
                    "language": {
                        "searchPlaceholder": "Search payslips...",
                        "paginate": {
                            "previous": "<i class='fas fa-chevron-left'></i>",
                            "next": "<i class='fas fa-chevron-right'></i>"
                        }
                    },
                    "rowCallback": function(row, data, displayIndex) {
                        $('td:first', row).html('<strong>' + (displayIndex + 1) + '</strong>');
                    }
                });
            }
        });
    </script>
@endpush
