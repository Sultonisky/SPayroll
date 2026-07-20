@extends('layouts.app')
@section('title', 'Preview Import - Attendance')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-eye me-2"></i>Preview Import Data
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendance-imports.create') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body">
                                    <div class="text-uppercase small fw-bold text-primary mb-1">Total Rows</div>
                                    <div class="fs-3 fw-bold text-body">{{ count($rows) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body">
                                    <div class="text-uppercase small fw-bold text-success mb-1">Valid Rows</div>
                                    <div class="fs-3 fw-bold text-success">{{ count($valid) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body">
                                    <div class="text-uppercase small fw-bold text-danger mb-1">Invalid Rows</div>
                                    <div class="fs-3 fw-bold text-danger">{{ count($invalid) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($invalid) > 0)
                        <div class="alert alert-danger border-left-danger shadow-sm mb-4">
                            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Invalid Rows Found</h6>
                            <ul class="mb-0">
                                @foreach($invalid as $inv)
                                    <li>Row {{ $inv['row_number'] }}: {{ implode(', ', $inv['errors']) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-dark small text-uppercase">
                                <tr>
                                    <th class="text-center">Row</th>
                                    <th>Employee NIK</th>
                                    <th>Date</th>
                                    <th class="text-center">Check In</th>
                                    <th class="text-center">Check Out</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    @php
                                        $isValid = true;
                                        foreach($invalid as $inv) {
                                            if($inv['row_number'] == $row['row_number']) {
                                                $isValid = false;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ $isValid ? '' : 'table-danger' }}">
                                        <td class="text-center fw-bold">{{ $row['row_number'] }}</td>
                                        <td>{{ $row['employee_nik'] }}</td>
                                        <td>{{ $row['attendance_date'] }}</td>
                                        <td class="text-center">{{ $row['check_in'] }}</td>
                                        <td class="text-center">{{ $row['check_out'] }}</td>
                                        <td class="text-center">
                                            @if($isValid)
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                        @if(count($valid) > 0)
                            <form action="{{ route('attendance-imports.store') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                    <i class="fas fa-save me-2"></i>Import Valid Data
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
