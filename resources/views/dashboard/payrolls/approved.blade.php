@extends('layouts.app')
@section('title', 'Approved Payroll')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-inbox me-2"></i>Approved Payroll
                    </h5>
                    @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                        @php
                            $exportParams = array_filter([
                                'year'        => $filterYear ?? null,
                                'month'       => $filterMonth ?? null,
                                'employee_id' => $filterEmployeeId ?? null,
                            ]);
                        @endphp
                        <a href="{{ route('payrolls.approved.export', $exportParams) }}"
                            class="btn btn-outline-success btn-sm rounded-pill px-3 px-md-4 shadow-sm fw-bold">
                            <i class="fas fa-file-csv me-2"></i>Export CSV
                        </a>
                    @endif
                </div>

                <div class="card-body pb-0">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('payrolls.approved') }}" id="filter-form">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Year</label>
                                <select name="year" class="form-select form-select-sm rounded-pill shadow-sm">
                                    <option value="">All Years</option>
                                    @foreach (range(date('Y'), date('Y') - 2) as $y)
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
                                <a href="{{ route('payrolls.approved') }}"
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

                <div class="card-body">
                    @if ($payrolls->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <p class="mb-1 fw-semibold">No approved payroll records found.</p>
                            <p class="small">Approved records will appear here after being approved from the Drafts page.</p>
                            <a href="{{ route('payrolls.drafts') }}" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
                                <i class="fas fa-file-alt me-2"></i>Go to Drafts
                            </a>
                        </div>
                    @else
                        {{-- Bulk mark paid section --}}
                        @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn bg-primary text-black btn-sm rounded-pill px-4 shadow-sm fw-bold"
                                    data-coreui-toggle="modal" data-coreui-target="#bulkMarkPaidModal">
                                    <i class="fas fa-check-double me-2"></i>Mark All as Paid ({{ $payrolls->count() }})
                                </button>
                            </div>
                        @endif

                        <table class="table table-hover align-middle" id="approvedTable">
                            <thead class="table-light text-dark small text-uppercase">
                                <tr>
                                    <th width="5%" class="text-center">No.</th>
                                    <th>Employee</th>
                                    <th>Period</th>
                                    <th>Pay Date</th>
                                    <th>Base Salary</th>
                                    <th>Bonus</th>
                                    <th>Total</th>
                                    <th width="15%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $payroll)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-body">{{ $payroll->employee?->name ?? '-' }}</div>
                                            <small class="text-muted font-monospace">{{ $payroll->employee?->employee_code ?? '' }}</small>
                                        </td>
                                        <td class="text-body">
                                            <span data-order="{{ $payroll->year * 100 + $payroll->month }}">
                                                {{ \Carbon\Carbon::create($payroll->year, $payroll->month)->translatedFormat('F Y') }}
                                            </span>
                                        </td>
                                        <td class="text-body">
                                            {{ $payroll->pay_date?->translatedFormat('d M Y') ?? '-' }}
                                        </td>
                                        <td class="text-body">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                                        <td class="text-body">
                                            @if ($payroll->bonus > 0)
                                                <span class="text-success fw-semibold">+ Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-body">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                <a href="{{ route('payrolls.show', $payroll->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Detail">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>
                                                @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                                    <form id="markPaidForm{{ $payroll->id }}" action="{{ route('payrolls.mark-paid', $payroll->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="button" class="btn btn-white btn-sm px-3" title="Mark as Paid"
                                                            data-coreui-toggle="modal"
                                                            data-coreui-target="#markPaidModal{{ $payroll->id }}">
                                                            <i class="fas fa-check text-primary"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('payrolls.export', $payroll->id) }}"
                                                        class="btn btn-white btn-sm px-3" title="Export CSV">
                                                        <i class="fas fa-download text-primary"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                            <x-modal id="markPaidModal{{ $payroll->id }}"
                                                title="Mark as Paid"
                                                type="primary"
                                                icon="fa-money-check-alt">
                                                <div class="text-center py-2">
                                                    <i class="fas fa-check text-primary fa-3x mb-3"></i>
                                                    <h5 class="fw-bold">Mark as Paid?</h5>
                                                    <p class="text-muted mb-1">Confirm payment for <strong>{{ $payroll->employee?->name }}</strong>?</p>
                                                    <p class="text-muted small mb-0">
                                                        {{ \Carbon\Carbon::create($payroll->year, $payroll->month)->translatedFormat('F Y') }}
                                                        - Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <x-slot:footer>
                                                    <button type="button"
                                                        class="btn bg-primary text-black rounded-pill px-4 shadow-sm fw-bold"
                                                        onclick="document.getElementById('markPaidForm{{ $payroll->id }}').submit()">
                                                        <i class="fas fa-check me-2"></i>Yes, Mark as Paid
                                                    </button>
                                                </x-slot:footer>
                                            </x-modal>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Mark Paid Modal --}}
    @if ((auth()->user()->isAdmin() || auth()->user()->role === 'HR') && $payrolls->isNotEmpty())
        <x-modal id="bulkMarkPaidModal"
            title="Mark All as Paid"
            type="primary"
            icon="fa-double-check">
            <div class="py-2">
                <i class="fas fa-check-double text-primary fa-3x mb-3"></i>
                <h5 class="fw-bold">Mark All as Paid?</h5>
                <p class="text-muted mb-1">This will mark <strong>{{ $payrolls->count() }}</strong> approved payroll records as paid.</p>
                <p class="text-muted small mb-0">Total amount: <strong>Rp {{ number_format($payrolls->sum('total_salary'), 0, ',', '.') }}</strong></p>
                <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
            </div>
            <x-slot:footer>
                <button type="button"
                    class="btn bg-primary text-black rounded-pill px-4 shadow-sm fw-bold"
                    onclick="document.getElementById('bulkMarkPaidForm').submit()">
                    <i class="fas fa-check-double me-2"></i>Yes, Mark All as Paid
                </button>
            </x-slot:footer>
        </x-modal>

        <form id="bulkMarkPaidForm" action="{{ route('payrolls.approved.mark-paid-all') }}" method="POST" class="d-none">
            @csrf
            @foreach ($payrolls as $payroll)
                <input type="hidden" name="ids[]" value="{{ $payroll->id }}">
            @endforeach
        </form>
    @endif
@endsection

@push('styles')
    <style>#approvedTable thead th { color: #000 !important; }</style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#approvedTable')) {
                var table = $('#approvedTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[2, "desc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [7] },
                        { "type": "num", "targets": [2] }
                    ],
                    "language": {
                        "searchPlaceholder": "Search approved payrolls...",
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
