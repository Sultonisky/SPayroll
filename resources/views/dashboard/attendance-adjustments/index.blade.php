@extends('layouts.app')
@section('title', 'Attendance Adjustments')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-edit me-2"></i>Attendance Adjustments
                    </h5>
                </div>

                <div class="card-body">
                    <table class="table table-hover align-middle" id="attendanceAdjustmentsTable">
                        <thead class="table-light text-dark small text-uppercase">
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Old Check In</th>
                                <th>New Check In</th>
                                <th>Old Check Out</th>
                                <th>New Check Out</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($adjustments as $adjustment)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-body">{{ $adjustment->attendanceRecord->employee->name }} ({{ $adjustment->attendanceRecord->employee->nik }})</td>
                                    <td class="text-body">{{ $adjustment->attendanceRecord->attendance_date->translatedFormat('d M Y') }}</td>
                                    <td>{{ $adjustment->old_check_in }}</td>
                                    <td>{{ $adjustment->new_check_in }}</td>
                                    <td>{{ $adjustment->old_check_out }}</td>
                                    <td>{{ $adjustment->new_check_out }}</td>
                                    <td>{{ $adjustment->reason }}</td>
                                    <td>
                                        @if($adjustment->status == 'pending')
                                            <span class="badge bg-warning rounded-pill">Pending</span>
                                        @elseif($adjustment->status == 'approved')
                                            <span class="badge bg-success rounded-pill">Approved</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                            <a href="{{ route('attendance-adjustments.show', $adjustment->id) }}" class="btn btn-white btn-sm px-3" title="Detail">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                            @if(auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                                @if($adjustment->status == 'pending')
                                                    <form action="{{ route('attendance-adjustments.approve', $adjustment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Approve">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('attendance-adjustments.reject', $adjustment->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Reject">
                                                            <i class="fas fa-times-circle text-danger"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #attendanceAdjustmentsTable thead th {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#attendanceAdjustmentsTable')) {
                $('#attendanceAdjustmentsTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[2, "desc"]],
                    "language": {
                        "searchPlaceholder": "Search adjustments...",
                        "paginate": {
                            "previous": "<i class='fas fa-chevron-left'></i>",
                            "next": "<i class='fas fa-chevron-right'></i>"
                        }
                    }
                });
            }
        });
    </script>
@endpush
