@extends('layouts.app')
@section('title', isset($isTrash) ? 'Attendance Management - Trash' : 'Attendance Management')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas {{ isset($isTrash) ? 'fa-trash-alt' : 'fa-calendar-check' }} me-2"></i>
                        {{ isset($isTrash) ? 'Attendances Deleted' : 'Attendances Management' }}
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($isTrash))
                            <a href="{{ route('attendances.index') }}"
                                class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        @else
                            @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR' || auth()->user()->role == 'manager')
                                <a href="{{ route('attendances.trash') }}"
                                    class="btn btn-outline-danger btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-trash me-2"></i>Trash
                                </a>
                                <a href="{{ route('attendances.create') }}"
                                    class="btn bg-primary fw-bold text-black btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-plus-circle me-2"></i>Add New
                                </a>
                                <button type="button"
                                    class="btn bg-info fw-bold text-white btn-sm rounded-pill px-3 px-md-4 shadow-sm"
                                    data-coreui-toggle="modal" data-coreui-target="#importModal">
                                    <i class="fas fa-file-import me-2"></i>Import
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                @if (isset($isTrash))
                    <div class="px-4 pt-3">
                        <div class="alert alert-warning border-left-warning shadow-sm mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Trash Information</h6>
                                    <p class="mb-0 small">Items in the trash for more than <strong>90
                                            days</strong> will be permanently deleted automatically by the system.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($attendances->isEmpty())
                    <div class="card-body">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p class="mb-0">No attendance data found.</p>
                        </div>
                    </div>
                @else
                    <div class="card-body attendances-table-wrap">
                        <table class="table table-hover align-middle" id="attendancesTable">
                            <thead class="table-light text-dark small text-uppercase">
                                <tr>
                                    <th width="5%" class="text-center">No.</th>
                                    <th>Employee</th>
                                    <th class="text-center">Latest Period</th>
                                    <th class="text-center">Latest Present</th>
                                    <th width="15%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($isTrash))
                                    @foreach ($attendances as $employeeId => $items)
                                        @foreach ($items as $attendance)
                                            @php
                                                $employeeName = $attendance->employee ? $attendance->employee->name : 'Unknown';
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-bold">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                                                <td class="fw-bold text-body">{{ $employeeName }}</td>
                                                <td class="text-center text-body">{{ $attendance->month }}/{{ $attendance->year }}</td>
                                                <td class="text-center text-body">{{ $attendance->present }}</td>
                                                <td class="text-center">
                                                    @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                            <a href="{{ route('attendances.show', $attendance->id) }}"
                                                                class="btn btn-white btn-sm px-3" title="Show">
                                                                <i class="fas fa-eye text-info"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-white btn-sm px-3"
                                                                data-coreui-toggle="modal"
                                                                data-coreui-target="#restoreModal{{ $attendance->id }}"
                                                                title="Restore">
                                                                <i class="fas fa-undo text-success"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-white btn-sm px-3"
                                                                data-coreui-toggle="modal"
                                                                data-coreui-target="#forceDeleteModal{{ $attendance->id }}"
                                                                title="Permanently Delete">
                                                                <i class="fas fa-times-circle text-danger"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>

                                            <x-modal id="restoreModal{{ $attendance->id }}" title="Restore Period"
                                                type="success" :actionUrl="route('attendances.restore', $attendance->id)" method="POST"
                                                confirmText="Restore">
                                                <div class="text-center py-3">
                                                    <i class="fas fa-undo text-success fa-3x mb-3"></i>
                                                    <h5 class="fw-bold">Restore Period?</h5>
                                                    <p class="text-muted mb-0">Are you sure you want to restore
                                                        <strong>{{ $employeeName }}</strong> period
                                                        {{ $attendance->month }}/{{ $attendance->year }}?
                                                    </p>
                                                </div>
                                            </x-modal>

                                            <x-modal id="forceDeleteModal{{ $attendance->id }}" title="Permanently Delete"
                                                type="danger" :actionUrl="route('attendances.force-delete', $attendance->id)" method="DELETE"
                                                confirmText="Permanently Delete">
                                                <div class="text-center py-3">
                                                    <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                    <h5 class="fw-bold">Permanent Action!</h5>
                                                    <p class="text-muted mb-0">Are you sure you want to permanently delete
                                                        <strong>{{ $employeeName }}</strong> period
                                                        {{ $attendance->month }}/{{ $attendance->year }}?
                                                    </p>
                                                    <p class="text-danger small mt-2 mb-0">This data cannot be recovered.</p>
                                                </div>
                                            </x-modal>
                                        @endforeach
                                    @endforeach
                                @else
                                    @foreach ($attendances as $employeeId => $items)
                                        @php
                                            $latest = $items->first();
                                            $employeeName = $latest->employee ? $latest->employee->name : 'Unknown';
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                            <td class="fw-bold text-body">{{ $employeeName }}</td>
                                            <td class="text-center text-body">{{ $latest->month }}/{{ $latest->year }}</td>
                                            <td class="text-center text-body">{{ $latest->present }}</td>
                                            <td class="text-center">
                                                @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR' || auth()->user()->role == 'manager' || auth()->user()->role == 'staff')
                                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                        <a href="{{ route('attendances.show', $latest->id) }}"
                                                            class="btn btn-white btn-sm px-3" title="Show">
                                                            <i class="fas fa-eye text-info"></i>
                                                        </a>
                                                        @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                                            <button type="button" class="btn btn-white btn-sm px-3"
                                                                data-coreui-toggle="modal"
                                                                data-coreui-target="#deleteModal{{ $latest->id }}"
                                                                title="Delete">
                                                                <i class="fas fa-trash-alt text-danger"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>

                                        <x-modal id="deleteModal{{ $latest->id }}" title="Delete Confirmation"
                                            type="danger" :actionUrl="route('attendances.destroy', $latest->id)" method="DELETE"
                                            confirmText="Move to Trash">
                                            <div class="text-center py-3">
                                                <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Delete Attendance?</h5>
                                                <p class="text-muted mb-0">Are you sure you want to move
                                                    <strong>{{ $employeeName }}</strong> period
                                                    {{ $latest->month }}/{{ $latest->year }} to trash?
                                                </p>
                                            </div>
                                        </x-modal>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Attendance Data</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('attendances.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <label for="file" class="form-label mb-0">Upload File (CSV, XLSX, XLS)</label>
                            <a href="{{ route('attendances.import.template') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>
                        </div>
                        <input type="file" name="file" class="form-control" id="file" accept=".csv,.xlsx,.xls" required>
                        <div class="form-text mt-2">
                            Columns: employee_id or employee_name, year, month, work_days, present, sick, leave, alpha, overtime_hours, notes
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #attendancesTable thead th {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#attendancesTable')) {
                $('#attendancesTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [
                        [0, "asc"]
                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": [4]
                    }],
                    "language": {
                        "searchPlaceholder": "Search employees...",
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
