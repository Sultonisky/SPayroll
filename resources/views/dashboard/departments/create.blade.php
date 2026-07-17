@extends('layouts.app')
@section('title', 'Add New Department')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-building me-2"></i>Add New Department
                    </h5>
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-building "></i></span>
                                    <input type="text" name="name"
                                        class="form-control border-start-0 @error('name') is-invalid @enderror"
                                        placeholder="Enter department name" value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-align-left"></i></span>
                                    <textarea name="description"
                                        class="form-control border-start-0 @error('description') is-invalid @enderror"
                                        placeholder="Enter department description">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset" class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
