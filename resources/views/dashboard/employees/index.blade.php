@extends('layouts.app')
@section('title', isset($isTrash) ? 'Employee Management - Trash' : 'Employee Management')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas {{ isset($isTrash) ? 'fa-trash-alt' : 'fa-users' }} me-2"></i>
                        {{ isset($isTrash) ? 'Employees Deleted' : 'Employees Management' }}
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($isTrash))
                            <a href="{{ route('employees.index') }}"
                                class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        @else
                            @if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager']))
                                @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                    <a href="{{ route('employees.trash') }}"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                        <i class="fas fa-trash me-2"></i>Trash
                                    </a>
                                @endif
                                <a href="{{ route('employees.create') }}"
                                    class="btn bg-primary fw-bold text-black btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-plus-circle me-2"></i>Add New
                                </a>
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

                <div class="card-body">
                    <table class="table table-hover align-middle" id="employeesTable">
                        <thead class="table-light text-dark small text-uppercase">
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th>NIK</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold text-body">{{ $employee->nik }}</td>
                                    <td class="fw-bold text-body">{{ $employee->name }}</td>
                                    <td class="text-body">{{ $employee->email }}</td>
                                    <td class="text-body">{{ $employee->department?->name ?? '-' }}</td>
                                    <td class="text-body">{{ $employee->position?->name ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-secondary' }} text-white rounded-pill px-3">
                                            {{ ucfirst($employee->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                            @if (isset($isTrash))
                                                @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                                    <form action="{{ route('employees.restore', $employee->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3"
                                                            title="Restore">
                                                            <i class="fas fa-undo text-success"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#forceDeleteModal{{ $employee->id }}"
                                                        title="Permanently Delete">
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('employees.show', $employee->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Detail">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>
                                                @if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager']))
                                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                                        class="btn btn-white btn-sm px-3" title="Edit">
                                                        <i class="fas fa-edit text-warning"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('employees.export', $employee->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Export">
                                                    <i class="fas fa-download text-primary"></i>
                                                </a>
                                                @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#deleteModal{{ $employee->id }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt text-danger"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    @if (isset($isTrash))
                                        <x-modal id="forceDeleteModal{{ $employee->id }}" title="Permanently Delete"
                                            type="danger" :actionUrl="route('employees.force-delete', $employee->id)" method="DELETE"
                                            confirmText="Permanently Delete">
                                            <div class="text-center py-3">
                                                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Permanent Action!</h5>
                                                <p class="text-muted mb-0">Are you sure you want to permanently delete
                                                    <strong>{{ $employee->name }}</strong>?</p>
                                                <p class="text-danger small mt-2 mb-0">This data cannot be recovered.</p>
                                            </div>
                                        </x-modal>
                                    @else
                                        <x-modal id="deleteModal{{ $employee->id }}" title="Delete Confirmation"
                                            type="danger" :actionUrl="route('employees.destroy', $employee->id)" method="DELETE"
                                            confirmText="Move to Trash">
                                            <div class="text-center py-3">
                                                <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Delete Employee?</h5>
                                                <p class="text-muted mb-0">Are you sure you want to move
                                                    <strong>{{ $employee->name }}</strong> to trash?</p>
                                            </div>
                                        </x-modal>
                                    @endif
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
        #employeesTable thead th {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#employeesTable')) {
                $('#employeesTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [
                        [0, "asc"]
                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": [7]
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
