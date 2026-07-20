@extends('layouts.app')
@section('title', 'Import Attendance')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-file-import me-2"></i>Import Attendance
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendance-imports.template') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                        <a href="{{ route('attendance-imports.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('attendance-imports.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Attendance File <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-file-upload"></i></span>
                                    <input type="file" name="file" class="form-control border-start-0 @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" required>
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted mt-2">
                                    Supported formats: .xlsx, .xls, .csv
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-eye me-2"></i>Preview & Validate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
