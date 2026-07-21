@extends('layouts.app')
@section('title', isset($isTrash) ? 'Payroll - Trash' : 'Payroll Management')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas {{ isset($isTrash) ? 'fa-trash-alt' : 'fa-file-invoice-dollar' }} me-2"></i>
                        @if (isset($isTrash))
                            Payroll Deleted
                        @elseif ($periodLabel ?? null)
                            Payroll Records — {{ $periodLabel }}
                        @else
                            Payroll Records
                        @endif
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($isTrash))
                            <a href="{{ route('payrolls.index') }}"
                                class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back
                        @elseif ($periodLabel ?? null)
                            <a href="{{ route('payrolls.periods') }}"
                                class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to Periods
                            </a>
                        @else
                            @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                <a href="{{ route('payrolls.trash') }}"
                                    class="btn btn-outline-danger btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-trash me-2"></i>Trash
                                </a>
                            @endif
                            @if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager']))
                                <a href="{{ route('payrolls.create') }}"
                                    class="btn bg-primary fw-bold text-black btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                                    <i class="fas fa-plus-circle me-2"></i>Add Manual
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
                @endif

                <div class="card-body">
                    <table class="table table-hover align-middle" id="payrollsTable">
                        <thead class="table-light text-dark small text-uppercase">
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Pay Date</th>
                                <th>Base Salary</th>
                                <th>Bonus</th>
                                <th>Total</th>
                                <th>Status</th>
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
                                        {{ \Carbon\Carbon::create($payroll->year, $payroll->month)->translatedFormat('F Y') }}
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
                                    <td>
                                        @php
                                            $color = match($payroll->status) {
                                                'paid'     => 'bg-success',
                                                'approved' => 'bg-info',
                                                default    => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $color }} text-white rounded-pill px-3">
                                            {{ ucfirst($payroll->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                            @if (isset($isTrash))
                                                @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                                    <form action="{{ route('payrolls.restore', $payroll->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Restore">
                                                            <i class="fas fa-undo text-success"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#forceDeleteModal{{ $payroll->id }}"
                                                        title="Permanently Delete">
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('payrolls.show', $payroll->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Detail">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>
                                                @if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager']))
                                                    <a href="{{ route('payrolls.edit', $payroll->id) }}"
                                                        class="btn btn-white btn-sm px-3" title="Edit">
                                                        <i class="fas fa-edit text-warning"></i>
                                                    </a>
                                                @endif
                                                @if ($payroll->isDraft() && (auth()->user()->isAdmin() || auth()->user()->role === 'HR'))
                                                    <form action="{{ route('payrolls.approve', $payroll->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Approve"
                                                            onclick="return confirm('Approve this payroll?')">
                                                            <i class="fas fa-check text-success"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($payroll->isApproved() && (auth()->user()->isAdmin() || auth()->user()->role === 'HR'))
                                                    <form action="{{ route('payrolls.mark-paid', $payroll->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-white btn-sm px-3" title="Mark Paid"
                                                            onclick="return confirm('Mark this payroll as paid?')">
                                                            <i class="fas fa-money-check-alt text-primary"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('payrolls.export', $payroll->id) }}"
                                                    class="btn btn-white btn-sm px-3" title="Export CSV">
                                                    <i class="fas fa-download text-primary"></i>
                                                </a>
                                                @if (auth()->user()->isAdmin() || auth()->user()->role === 'HR')
                                                    <button type="button" class="btn btn-white btn-sm px-3"
                                                        data-coreui-toggle="modal"
                                                        data-coreui-target="#deleteModal{{ $payroll->id }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt text-danger"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    @if (isset($isTrash))
                                        <x-modal id="forceDeleteModal{{ $payroll->id }}" title="Permanently Delete"
                                            type="danger" :actionUrl="route('payrolls.force-delete', $payroll->id)" method="DELETE"
                                            confirmText="Permanently Delete">
                                            <div class="text-center py-3">
                                                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Permanent Action!</h5>
                                                <p class="text-muted">This payroll record cannot be recovered.</p>
                                            </div>
                                        </x-modal>
                                    @else
                                        <x-modal id="deleteModal{{ $payroll->id }}" title="Delete Payroll"
                                            type="danger" :actionUrl="route('payrolls.destroy', $payroll->id)" method="DELETE"
                                            confirmText="Move to Trash">
                                            <div class="text-center py-3">
                                                <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                                                <h5 class="fw-bold">Delete Payroll?</h5>
                                                <p class="text-muted">Move this payroll record to trash?</p>
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
    <style>#payrollsTable thead th { color: #000 !important; }</style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#payrollsTable')) {
                $('#payrollsTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[2, "desc"]],
                    "columnDefs": [{ "orderable": false, "targets": [8] }],
                    "language": {
                        "searchPlaceholder": "Search payrolls...",
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
