@extends('layouts.app')
@section('title', isset($isTrash) ? 'Bonuses - Trash' : 'Bonus Management')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas {{ isset($isTrash) ? 'fa-trash-alt' : 'fa-gift' }} me-2"></i>
                        {{ isset($isTrash) ? 'Bonuses Deleted' : 'Bonus Management' }}
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($isTrash))
                            <a href="{{ route('bonuses.index') }}"
                                class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        @else
                            @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                <a href="{{ route('bonuses.trash') }}"
                                    class="btn btn-outline-danger btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-trash me-2"></i>Trash
                                </a>
                            @endif
                            @if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager']))
                                <a href="{{ route('bonuses.create') }}"
                                    class="btn bg-primary fw-bold text-black btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-plus-circle me-2"></i>Add Bonus
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

                @if (isset($isTrash))
                    <div class="px-4 pt-3">
                        <div class="alert alert-warning shadow-sm mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Items in the trash for more than <strong>90 days</strong> will be permanently deleted.
                        </div>
                    </div>
                @else
                    <div class="card-body pb-0">
                        <form method="GET" action="{{ route('bonuses.index') }}" id="filter-form">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Year</label>
                                    <select name="year" id="filterYear" class="form-select form-select-sm rounded-pill shadow-sm">
                                        @for ($y = date('Y') - 2; $y <= date('Y'); $y++)
                                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Month</label>
                                    <select name="month" id="filterMonth" class="form-select form-select-sm rounded-pill shadow-sm">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Employee</label>
                                    <select name="employee_id" id="filterEmployee" class="form-select form-select-sm rounded-pill shadow-sm">
                                        <option value="">All Employees</option>
                                        @foreach ($allEmployees as $emp)
                                            <option value="{{ $emp->id }}" {{ ($employeeId ?? '') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }} ({{ $emp->nik }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Status</label>
                                    <select name="status" id="filterStatus" class="form-select form-select-sm rounded-pill shadow-sm">
                                        <option value="">All Status</option>
                                        <option value="pending"  {{ ($status ?? '') == 'pending'  ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-end justify-content-end gap-2">
                                    <a href="{{ route('bonuses.index') }}"
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
                    <table class="table table-hover align-middle" id="bonusesTable">
                        <thead class="table-light text-dark small text-uppercase">
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bonuses as $bonus)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-body">{{ $bonus->employee?->name ?? '-' }}</div>
                                        <small class="text-muted font-monospace">{{ $bonus->employee?->employee_code ?? '' }}</small>
                                    </td>
                                    <td class="text-body">
                                        {{ \Carbon\Carbon::create($bonus->year, $bonus->month)->translatedFormat('F Y') }}
                                    </td>
                                    <td><span class="badge bg-body text-body border rounded-pill px-3">{{ $bonus->type }}</span></td>
                                    <td class="fw-bold text-body">
                                        Rp {{ number_format($bonus->amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                            $color = match($bonus->status) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                default    => 'bg-warning text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $color }} rounded-pill px-3">
                                            {{ ucfirst($bonus->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                            @if (isset($isTrash))
                                                @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                                    <form action="{{ route('bonuses.restore', $bonus->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Restore">
                                                            <i class="fas fa-undo text-success"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#forceDeleteModal{{ $bonus->id }}"
                                                        title="Permanently Delete">
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('bonuses.show', $bonus->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Detail">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>
                                                @if ($bonus->isPending() && (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager'])))
                                                    <a href="{{ route('bonuses.edit', $bonus->id) }}"
                                                        class="btn btn-white btn-sm px-3" title="Edit">
                                                        <i class="fas fa-edit text-warning"></i>
                                                    </a>
                                                @endif
                                                @if ($bonus->isPending() && (auth()->user()->isAdmin() || auth()->user()->role === 'HR'))
                                                    <form action="{{ route('bonuses.approve', $bonus->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Approve"
                                                            onclick="return confirm('Approve this bonus?')">
                                                            <i class="fas fa-check text-success"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#rejectModal{{ $bonus->id }}"
                                                        title="Reject">
                                                        <i class="fas fa-times text-danger"></i>
                                                    </button>
                                                @endif
                                                @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#deleteModal{{ $bonus->id }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt text-danger"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    @if (isset($isTrash))
                                        <x-modal id="forceDeleteModal{{ $bonus->id }}" title="Permanently Delete"
                                            type="danger" :actionUrl="route('bonuses.force-delete', $bonus->id)" method="DELETE"
                                            confirmText="Permanently Delete">
                                            <div class="text-center py-3">
                                                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Permanent Action!</h5>
                                                <p class="text-muted">Are you sure you want to permanently delete this bonus?</p>
                                                <p class="text-danger small">This cannot be recovered.</p>
                                            </div>
                                        </x-modal>
                                    @else
                                        <x-modal id="deleteModal{{ $bonus->id }}" title="Delete Bonus"
                                            type="danger" :actionUrl="route('bonuses.destroy', $bonus->id)" method="DELETE"
                                            confirmText="Move to Trash">
                                            <div class="text-center py-3">
                                                <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Delete Bonus?</h5>
                                                <p class="text-muted">Move this bonus record to trash?</p>
                                            </div>
                                        </x-modal>

                                        {{-- Reject Modal --}}
                                        @if ($bonus->isPending())
                                            <div class="modal fade" id="rejectModal{{ $bonus->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('bonuses.reject', $bonus->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Bonus</h5>
                                                                <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <label class="form-label fw-bold">Reason (optional)</label>
                                                                <textarea name="notes" class="form-control" rows="3"
                                                                    placeholder="Enter reason for rejection..."></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-coreui-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
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
    <style>#bonusesTable thead th { color: #000 !important; }</style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#bonusesTable')) {
                $('#bonusesTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[2, "desc"]],
                    "columnDefs": [{ "orderable": false, "targets": [6] }],
                    "language": {
                        "searchPlaceholder": "Search bonuses...",
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
