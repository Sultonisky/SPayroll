@extends('layouts.app')
@section('title', isset($isTrash) ? 'User Management - Trash' : 'User Management')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas {{ isset($isTrash) ? 'fa-trash-alt' : 'fa-users-cog' }} me-2"></i>
                        {{ isset($isTrash) ? 'Users Deleted' : 'Users Management' }}
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($isTrash))
                            <a href="{{ route('users.index') }}"
                                class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        @else
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('users.trash') }}"
                                    class="btn btn-outline-danger btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-trash me-2"></i>Trash
                                </a>
                                <a href="{{ route('users.create') }}"
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
                    <table class="table table-hover align-middle" id="usersTable">
                        <thead class="table-light text-dark small text-uppercase">
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">Role</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $displayUsers = $users;
                                if (auth()->user()->isAdmin() && !isset($isTrash)) {
                                    $displayUsers = $users->where('role', '!=', 'admin');
                                }
                            @endphp
                            @foreach ($displayUsers as $user)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-body">{{ $user->name }}</td>
                                    <td class="text-body">
                                        {{ $user->email }}
                                    </td>
                                    <td class="text-center">
                                        @if ($user->role == 'admin')
                                            <span class="badge bg-danger-subtle text-black rounded-pill px-3">
                                                <i class="fas fa-shield-alt me-1"></i> Admin
                                            </span>
                                        @elseif($user->role == 'HR')
                                            <span class="badge bg-info text-white rounded-pill px-3">
                                                <i class="fas fa-user-tie me-1"></i> HR
                                            </span>
                                        @elseif($user->role == 'manager')
                                            <span class="badge bg-success text-white rounded-pill px-3">
                                                <i class="fas fa-chart-line me-1"></i> Manager
                                            </span>
                                        @elseif($user->role == 'staff')
                                            <span class="badge bg-warning text-white rounded-pill px-3">
                                                <i class="fas fa-user me-1"></i> Staff
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (auth()->id() !== $user->id)
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                                @if (isset($isTrash))
                                                    @if (auth()->user()->isAdmin())
                                                        <form action="{{ route('users.restore', $user->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-white btn-sm px-3"
                                                                title="Restore">
                                                                <i class="fas fa-undo text-success"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-white btn-sm px-3"
                                                            data-coreui-toggle="modal"
                                                            data-coreui-target="#forceDeleteModal{{ $user->id }}"
                                                            title="Permanently Delete">
                                                            <i class="fas fa-times-circle text-danger"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                        class="btn btn-white btn-sm px-3" title="Detail">
                                                        <i class="fas fa-eye text-info"></i>
                                                    </a>
                                                    @if (auth()->user()->isAdmin())
                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                            class="btn btn-white btn-sm px-3" title="Edit">
                                                            <i class="fas fa-edit text-warning"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-white btn-sm px-3"
                                                            data-coreui-toggle="modal"
                                                            data-coreui-target="#deleteModal{{ $user->id }}"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt text-danger"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted rounded-pill px-3">
                                                <i class="fas fa-user-circle me-1"></i> Your Account
                                            </span>
                                        @endif

                                        @if (auth()->id() !== $user->id)
                                            @if (isset($isTrash))
                                                <x-modal id="forceDeleteModal{{ $user->id }}" title="Permanently Delete"
                                                    type="danger" :actionUrl="route('users.force-delete', $user->id)" method="DELETE"
                                                    confirmText="Permanently Delete">
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                        <h5 class="fw-bold">Permanent Action!</h5>
                                                        <p class="text-muted mb-0">Are you sure you want to permanently delete
                                                            <strong>{{ $user->name }}</strong>?</p>
                                                        <p class="text-danger small mt-2 mb-0">This data cannot be recovered.</p>
                                                    </div>
                                                </x-modal>
                                            @else
                                                <x-modal id="deleteModal{{ $user->id }}" title="Delete Confirmation"
                                                    type="danger" :actionUrl="route('users.destroy', $user->id)" method="DELETE"
                                                    confirmText="Move to Trash">
                                                    <div class="text-center py-3">
                                                        <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                                                        <h5 class="fw-bold">Delete User?</h5>
                                                        <p class="text-muted mb-0">Are you sure you want to move
                                                            <strong>{{ $user->name }}</strong> to trash?</p>
                                                    </div>
                                                </x-modal>
                                            @endif
                                        @endif
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
        #usersTable thead th {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [
                        [0, "asc"]
                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": [3]
                    }],
                    "language": {
                        "searchPlaceholder": "Search users...",
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
