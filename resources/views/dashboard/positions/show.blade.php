@extends('layouts.app')
@section('title', 'Position Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div
                    class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-id-card me-2"></i>Position Information
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (auth()->user()->isAdmin() || auth()->user()->role == 'HR')
                            <a href="{{ route('positions.edit', $position->id) }}"
                                class="btn btn-warning btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                        @endif
                        <a href="{{ route('positions.index') }}"
                            class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-auto text-center mb-3 mb-md-0">
                            <div class="d-inline-block position-relative">
                                <div class="text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-primary"
                                    style="width: 140px; height: 140px;">
                                    <i class="fas fa-briefcase fa-5x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md ms-md-4 text-center text-md-start">
                            <h2 class="fw-bold text-body mb-1">{{ $position->name }}</h2>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-calendar-check me-2 text-info"></i>Created:
                                    {{ $position->created_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Position Name</div>
                                    <div class="fs-5 fw-bold text-body">{{ $position->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Position ID</div>
                                    <div class="fs-5 fw-bold text-body">
                                        #{{ str_pad($position->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Base Salary</div>
                                    <div class="fs-5 fw-bold text-body">
                                        {{ number_format($position->base_salary, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Description</div>
                                    <div class="fs-6 fw-normal text-body">{{ $position->description }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Created At</div>
                                    <div class="fs-6 fw-bold text-body">
                                        {{ $position->created_at->translatedFormat('d F Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Last Updated</div>
                                    <div class="fs-6 fw-bold text-body">
                                        {{ $position->updated_at->translatedFormat('d F Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top text-center text-muted small italic">
                        <i class="fas fa-info-circle me-1"></i> Information retrieved from system database on
                        {{ now()->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
