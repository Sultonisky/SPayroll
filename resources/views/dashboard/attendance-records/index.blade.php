@extends('layouts.app')
@section('title', 'Monthly Attendance Summary')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Attendance Summary
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row mb-4 g-3">
                        <div class="col-md-2">
                            <select name="year" id="filterYear" class="form-select">
                                @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="month" id="filterMonth" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="employee" id="filterEmployee" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($allEmployees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->nik }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="applyFilter" class="btn btn-primary w-100">Apply Filter</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="attendanceSummaryTable">
                            <thead class="table-light text-dark small text-uppercase">
                                <tr>
                                    <th width="15%">Employee</th>
                                    @foreach($days as $day)
                                        <th width="3%" class="text-center">{{ $day }}</th>
                                    @endforeach
                                    <th width="5%" class="text-center">P</th>
                                    <th width="5%" class="text-center">L</th>
                                    <th width="5%" class="text-center">S</th>
                                    <th width="5%" class="text-center">A</th>
                                    <th width="5%" class="text-center">OT (h)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td class="fw-bold">{{ $employee->name }}</td>
                                        @php
                                            $present = 0;
                                            $late = 0;
                                            $sick = 0;
                                            $absent = 0;
                                            $totalOvertime = 0;
                                            $employeeRecords = $employee->attendanceRecords->keyBy(function($item) {
                                                return $item->attendance_date->toDateString();
                                            });
                                        @endphp
                                        @foreach($days as $day)
                                            @php
                                                $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                                $record = $employeeRecords->get($date);
                                                $statusClass = 'bg-secondary';
                                                $statusIcon = '?';
                                                if ($record) {
                                                    if ($record->attendance_status == 'present') {
                                                        $present++;
                                                        $statusClass = 'bg-success';
                                                        $statusIcon = 'P';
                                                    } elseif ($record->attendance_status == 'late') {
                                                        $late++;
                                                        $present++;
                                                        $statusClass = 'bg-warning';
                                                        $statusIcon = 'L';
                                                    } elseif ($record->attendance_status == 'sick') {
                                                        $sick++;
                                                        $statusClass = 'bg-info';
                                                        $statusIcon = 'S';
                                                    } elseif ($record->attendance_status == 'leave') {
                                                        $statusClass = 'bg-primary';
                                                        $statusIcon = 'LV';
                                                    } elseif ($record->attendance_status == 'absent') {
                                                        $absent++;
                                                        $statusClass = 'bg-danger';
                                                        $statusIcon = 'A';
                                                    } elseif ($record->attendance_status == 'need_review') {
                                                        $statusClass = 'bg-warning text-dark';
                                                        $statusIcon = 'R';
                                                    }
                                                    $totalOvertime += $record->overtime_minutes / 60;
                                                }
                                                $recordId = $record ? $record->id : null;
                                            @endphp
                                            <td class="text-center">
                                                @if($recordId)
                                                    <a href="{{ route('attendance-records.show', $recordId) }}" class="badge {{ $statusClass }} rounded-pill d-inline-block text-center" style="width: 24px; height: 24px; line-height: 18px;">{{ $statusIcon }}</a>
                                                @else
                                                    <span class="badge {{ $statusClass }} rounded-pill d-inline-block" style="width: 24px; height: 24px; line-height: 18px;">{{ $statusIcon }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bold text-success">{{ $present }}</td>
                                        <td class="text-center fw-bold text-warning">{{ $late }}</td>
                                        <td class="text-center fw-bold text-info">{{ $sick }}</td>
                                        <td class="text-center fw-bold text-danger">{{ $absent }}</td>
                                        <td class="text-center fw-bold">{{ number_format($totalOvertime, 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold text-primary mb-2">Legend</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <span class="badge bg-success rounded-pill">P = Present</span>
                            <span class="badge bg-warning rounded-pill">L = Late</span>
                            <span class="badge bg-info rounded-pill">S = Sick</span>
                            <span class="badge bg-primary rounded-pill">LV = Leave</span>
                            <span class="badge bg-danger rounded-pill">A = Absent</span>
                            <span class="badge bg-warning text-dark rounded-pill">R = Need Review</span>
                            <span class="badge bg-secondary rounded-pill">? = No Record</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #attendanceSummaryTable thead th {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#attendanceSummaryTable')) {
                $('#attendanceSummaryTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[0, "asc"]],
                    "scrollX": true,
                    "language": {
                        "searchPlaceholder": "Search employees...",
                        "paginate": {
                            "previous": "<i class='fas fa-chevron-left'></i>",
                            "next": "<i class='fas fa-chevron-right'></i>"
                        }
                    }
                });
            }

            $('#applyFilter').on('click', function() {
                let params = new URLSearchParams();
                let selectedYear = $('#filterYear').val();
                let selectedMonth = $('#filterMonth').val();
                let selectedEmployee = $('#filterEmployee').val();

                if (selectedYear) params.set('year', selectedYear);
                if (selectedMonth) params.set('month', selectedMonth);
                if (selectedEmployee) params.set('employee_id', selectedEmployee);

                window.location.href = `{{ route('attendance-records.index') }}?${params.toString()}`;
            });
        });
    </script>
@endpush
