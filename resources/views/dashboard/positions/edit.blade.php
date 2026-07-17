@extends('layouts.app')
@section('title', 'Edit Position')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-edit me-2"></i>Edit Position
                    </h5>
                    <a href="{{ route('positions.index') }}"
                        class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('positions.update', $position->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Position Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-briefcase"></i></span>
                                    <input type="text" name="name"
                                        class="form-control border-start-0 @error('name') is-invalid @enderror"
                                        placeholder="Enter position name"
                                        value="{{ old('name', $position->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Base Salary --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Base Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="base_salary" step="0.01"
                                        class="form-control border-start-0 @error('base_salary') is-invalid @enderror"
                                        placeholder="Enter base salary"
                                        value="{{ old('base_salary', $position->base_salary) }}" required>
                                </div>
                                @error('base_salary')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0 text-black"><i
                                            class="fas fa-align-left"></i></span>
                                    <textarea name="description"
                                        class="form-control border-start-0 @error('description') is-invalid @enderror"
                                        placeholder="Enter position description">{{ old('description', $position->description) }}</textarea>
                                </div>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-4 border-top">
                            <button type="reset"
                                class="btn btn-sm btn-outline-danger rounded-pill px-4 border shadow-sm">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit"
                                class="btn btn-sm bg-primary rounded-pill px-md-5 shadow-sm fw-bold text-black">
                                <i class="fas fa-check-circle me-2"></i>Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
