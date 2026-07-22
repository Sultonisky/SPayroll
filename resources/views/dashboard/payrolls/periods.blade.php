@extends('layouts.app')
@section('title', 'Payroll Periods')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-calendar-check me-2"></i>All Payroll Periods
                    </h5>
                    @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                        <a href="{{ route('payrolls.generate') }}"
                            class="btn bg-primary text-black btn-sm rounded-pill px-3 px-md-4 shadow-sm fw-bold">
                            <i class="fas fa-play-circle me-2"></i>Run Payroll
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('payrolls.periods') }}" id="filter-form">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">Year</label>
                                <select name="year" class="form-select form-select-sm rounded-pill shadow-sm">
                                    <option value="">All Years</option>
                                    @foreach ($availableYears as $y)
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
                                <label class="form-label small fw-bold text-muted">Status</label>
                                <select name="period_status" class="form-select form-select-sm rounded-pill shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="paid"     {{ ($filterStatus ?? '') === 'paid'     ? 'selected' : '' }}>Fully Paid</option>
                                    <option value="approved" {{ ($filterStatus ?? '') === 'approved' ? 'selected' : '' }}>Approved (not fully paid)</option>
                                    <option value="draft"    {{ ($filterStatus ?? '') === 'draft'    ? 'selected' : '' }}>Has Draft</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-end justify-content-end gap-2">
                                <a href="{{ route('payrolls.periods') }}"
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

                    @if ($periods->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                            <p class="mb-1 fw-semibold">No payroll periods found.</p>
                            @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                <a href="{{ route('payrolls.generate') }}" class="btn btn-success btn-sm rounded-pill mt-2">
                                    <i class="fas fa-play-circle me-2"></i>Run First Payroll
                                </a>
                            @endif
                        </div>
                    @else
                        <table class="table table-hover align-middle" id="periodsTable">
                            <thead class="table-light text-dark small text-uppercase">
                                <tr>
                                    <th width="5%" class="text-center">No.</th>
                                    <th>Period</th>
                                    <th class="text-center">Employees</th>
                                    <th>Total Base Salary</th>
                                    <th>Total Bonus</th>
                                    <th>Grand Total</th>
                                    <th class="text-center">Progress</th>
                                    <th width="12%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($periods as $period)
                                    @php
                                        $total     = $period->total_employees;
                                        $paidPct   = $total > 0 ? round(($period->paid_count / $total) * 100) : 0;
                                        $approvedPct = $total > 0 ? round(($period->approved_count / $total) * 100) : 0;
                                        $draftPct  = $total > 0 ? round(($period->draft_count / $total) * 100) : 0;

                                        // Overall status label for the period
                                        $periodStatus = 'draft';
                                        if ($period->paid_count === $total)     $periodStatus = 'paid';
                                        elseif ($period->draft_count === 0)     $periodStatus = 'approved';

                                        $statusColor = match($periodStatus) {
                                            'paid'     => 'bg-success',
                                            'approved' => 'bg-info',
                                            default    => 'bg-secondary',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <span data-order="{{ $period->year * 100 + $period->month }}">
                                                <div class="fw-bold text-body fs-6">
                                                    {{ \Carbon\Carbon::create($period->year, $period->month)->translatedFormat('F Y') }}
                                                </div>
                                                @if ($period->pay_date)
                                                    <small class="text-muted">
                                                        Pay date: {{ \Carbon\Carbon::parse($period->pay_date)->translatedFormat('d M Y') }}
                                                    </small>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-body text-body border rounded-pill px-3 fs-6">
                                                {{ $period->total_employees }}
                                            </span>
                                        </td>
                                        <td class="text-body">
                                            Rp {{ number_format($period->total_base_salary, 0, ',', '.') }}
                                        </td>
                                        <td class="text-body">
                                            @if ($period->total_bonus > 0)
                                                <span class="text-success fw-semibold">
                                                    + Rp {{ number_format($period->total_bonus, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-body">
                                            Rp {{ number_format($period->total_salary, 0, ',', '.') }}
                                        </td>
                                        <td style="min-width: 160px;">
                                            {{-- Stacked progress: paid / approved / draft --}}
                                            <div class="progress mb-1" style="height: 8px; border-radius: 4px;">
                                                <div class="progress-bar bg-success" style="width: {{ $paidPct }}%"
                                                    title="{{ $period->paid_count }} Paid"></div>
                                                <div class="progress-bar bg-info" style="width: {{ $approvedPct }}%"
                                                    title="{{ $period->approved_count }} Approved"></div>
                                                <div class="progress-bar bg-secondary" style="width: {{ $draftPct }}%"
                                                    title="{{ $period->draft_count }} Draft"></div>
                                            </div>
                                            <div class="d-flex gap-2 small text-muted">
                                                @if ($period->paid_count > 0)
                                                    <span><i class="fas fa-circle text-success" style="font-size:.55rem"></i> {{ $period->paid_count }} paid</span>
                                                @endif
                                                @if ($period->approved_count > 0)
                                                    <span><i class="fas fa-circle text-info" style="font-size:.55rem"></i> {{ $period->approved_count }} approved</span>
                                                @endif
                                                @if ($period->draft_count > 0)
                                                    <span><i class="fas fa-circle text-secondary" style="font-size:.55rem"></i> {{ $period->draft_count }} draft</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('payrolls.index', ['year' => $period->year, 'month' => $period->month]) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                title="View records for this period">
                                                <i class="fas fa-eye me-1"></i>Records
                                            </a>
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
    <style>#periodsTable thead th { color: #000 !important; }</style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#periodsTable')) {
                var table = $('#periodsTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[1, "desc"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [6, 7] },
                        { "type": "num", "targets": [1] }
                    ],
                    "language": {
                        "searchPlaceholder": "Search periods...",
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
