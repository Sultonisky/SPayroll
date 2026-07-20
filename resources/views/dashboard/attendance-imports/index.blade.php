@extends('layouts.app')
@section('title', 'Import History - Attendance')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-history me-2"></i>Import History
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendance-imports.create') }}" class="btn bg-primary fw-bold text-black btn-sm rounded-pill px-3 px-md-4 shadow-sm">
                            <i class="fas fa-plus-circle me-2"></i>Import New
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-hover align-middle" id="attendanceImportsTable">
                        <thead class="table-light text-dark small text-uppercase">
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th>File Name</th>
                                <th>Imported By</th>
                                <th class="text-center">Total Rows</th>
                                <th class="text-center">Success</th>
                                <th class="text-center">Failed</th>
                                <th>Status</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($imports as $import)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-body">{{ $import->file_name }}</td>
                                    <td class="text-body">{{ $import->importedBy->name }}</td>
                                    <td class="text-center">{{ $import->total_rows }}</td>
                                    <td class="text-center text-success">{{ $import->success_rows }}</td>
                                    <td class="text-center text-danger">{{ $import->failed_rows }}</td>
                                    <td>
                                        @if($import->status == 'pending')
                                            <span class="badge bg-warning rounded-pill">Pending</span>
                                        @elseif($import->status == 'completed')
                                            <span class="badge bg-success rounded-pill">Completed</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill">Failed</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                            <a href="{{ route('attendance-imports.show', $import->id) }}" class="btn btn-white btn-sm px-3" title="Detail">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
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
        #attendanceImportsTable thead th {
            color: #000 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#attendanceImportsTable')) {
                $('#attendanceImportsTable').DataTable({
                    "dom": '<"dt-controls"Bf>r<"table-responsive"t><"dt-footer"ip>',
                    "order": [[0, "asc"]],
                    "language": {
                        "searchPlaceholder": "Search imports...",
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
