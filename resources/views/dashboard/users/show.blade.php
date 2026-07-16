@extends('layouts.app')
@section('title', 'User Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-id-card me-2"></i>User Profile Information
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if(auth()->id() !== $user->id)
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                            @endif
                        @else
                            <a href="{{ route('profile.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                                <i class="fas fa-user-cog me-2"></i>Profile Settings
                            </a>
                        @endif
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm rounded-pill px-3 px-md-4 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row align-items-center mb-5">
                        <div class="col-md-auto text-center mb-3 mb-md-0">
                            <div class="d-inline-block position-relative">
                                @if($user->foto_url)
                                    <img class="rounded-circle border border-4 border-white shadow-sm" 
                                        src="{{ $user->foto_url }}" 
                                        alt="{{ $user->name }}" 
                                        style="width: 140px; height: 140px; object-fit: cover;">
                                @else
                                    <div class=" text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-4 border-primary" 
                                        style="width: 140px; height: 140px;">
                                        <i class="fas fa-user fa-5x"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md ms-md-4 text-center text-md-start">
                            <h2 class="fw-bold text-body mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-3 fs-5"><i class="fas fa-envelope me-2 text-primary"></i>{{ $user->email }}</p>
                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                @if ($user->role == 'admin')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-4 py-2 fs-6">
                                        <i class="fas fa-shield-alt me-2"></i>Administrator
                                    </span>
                                @elseif ($user->role == 'HR')
                                    <span class="badge bg-info text-white rounded-pill px-4 py-2 fs-6">
                                        <i class="fas fa-user-tie me-2"></i>HR
                                    </span>
                                @elseif ($user->role == 'manager')
                                    <span class="badge bg-success text-white rounded-pill px-4 py-2 fs-6">
                                        <i class="fas fa-chart-line me-2"></i>Manager
                                    </span>
                                @elseif ($user->role == 'staff')
                                    <span class="badge bg-warning text-white rounded-pill px-4 py-2 fs-6">
                                        <i class="fas fa-user me-2"></i>Staff
                                    </span>
                                @endif
                                <span class="badge bg-body text-body border rounded-pill px-4 py-2 fs-6">
                                    <i class="fas fa-calendar-check me-2 text-info"></i>Joined: {{ $user->created_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Full Name</div>
                                    <div class="fs-5 fw-bold text-body">{{ $user->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Email Address</div>
                                    <div class="fs-5 fw-bold text-body text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">System Role</div>
                                    <div class="fs-5 fw-bold text-body text-capitalize">{{ $user->role }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Account Created</div>
                                    <div class="fs-6 fw-bold text-body">{{ $user->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Last Updated</div>
                                    <div class="fs-6 fw-bold text-body">{{ $user->updated_at->translatedFormat('d F Y, H:i') }} WIB</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">User ID</div>
                                    <div class="fs-5 fw-bold text-body">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top text-center text-muted small italic">
                        <i class="fas fa-info-circle me-1"></i> Information retrieved from system database on {{ now()->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
