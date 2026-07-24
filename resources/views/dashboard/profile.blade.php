@extends('layouts.app')
@section('title', 'Pengaturan Profil')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div
                    class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5 fs-md-4">
                        <i class="fas fa-user-cog me-2"></i>Profile Settings
                    </h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->isDemo())
                        {{-- ── Demo: full read-only view ── --}}
                        <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 py-2 px-3 mb-4">
                            <i class="fas fa-lock"></i>
                            <span class="small fw-semibold">This is a demo account. Profile cannot be edited. Data resets daily.</span>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="d-flex align-items-center gap-4">
                                    <img class="rounded-circle shadow-sm border"
                                        src="{{ $user->foto_url ?: asset('assets/images/logo.svg') }}"
                                        alt="Profile Photo"
                                        style="width:120px;height:120px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold fs-5">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                        <span class="badge mt-1
                                            @if($user->role === 'admin') bg-danger text-black
                                            @elseif($user->role === 'HR') bg-info text-white
                                            @elseif($user->role === 'manager') bg-success text-white
                                            @else bg-warning text-black @endif
                                            rounded-pill px-3">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Fullname</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0"><i class="fas fa-user text-black"></i></span>
                                    <input type="text" class="form-control border-start-0" value="{{ $user->name }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0"><i class="fas fa-envelope text-black"></i></span>
                                    <input type="email" class="form-control border-start-0" value="{{ $user->email }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Role</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0"><i class="fas fa-user-tag text-black"></i></span>
                                    <input type="text" class="form-control border-start-0" value="{{ ucfirst($user->role) }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary border-end-0"><i class="fas fa-lock text-black"></i></span>
                                    <input type="text" class="form-control border-start-0" value="••••••••••" disabled>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- ── Normal: editable form ── --}}
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="position-relative">
                                            <img id="avatarPreview" class="avatar-preview rounded-circle shadow-sm border"
                                                src="{{ $user->foto_url ?: asset('assets/images/logo.svg') }}" alt="Foto Profil"
                                                style="width: 120px; height: 120px; object-fit: cover;">
                                        </div>
                                        <div>
                                            <label class="form-label fw-bold small text-uppercase text-muted">Foto Profile</label>
                                            <input type="file" name="foto"
                                                class="form-control @error('foto') is-invalid @enderror" accept="image/*"
                                                onchange="previewFoto(this, '#avatarPreview')">
                                            @error('foto')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text small mt-2">JPG/PNG/GIF, maks 5MB.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Fullname <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name"
                                            class="form-control border-start-0 @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    @error('name')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email"
                                            class="form-control border-start-0 @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    @error('email')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-user-tag"></i></span>
                                        <input type="text" class="form-control border-start-0" value="{{ $user->role }}" disabled>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mt-4 mb-3 pb-2 border-bottom">
                                        <h6 class="text-primary fw-bold mb-0">
                                            <i class="fas fa-lock me-2"></i>Account Security
                                            <span class="fw-normal text-muted small ms-1">(Optional)</span>
                                        </h6>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-key"></i></span>
                                        <input type="password" name="current_password"
                                            class="form-control border-start-0 @error('current_password') is-invalid @enderror"
                                            placeholder="Confirm current password">
                                    </div>
                                    @error('current_password')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password"
                                            class="form-control border-start-0 @error('password') is-invalid @enderror"
                                            placeholder="Minimal 8 characters">
                                    </div>
                                    @error('password')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary border-end-0 text-black"><i class="fas fa-check-double"></i></span>
                                        <input type="password" name="password_confirmation" class="form-control border-start-0"
                                            placeholder="Re-enter new password">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn bg-primary fw-bold text-black px-md-5 rounded-pill shadow-sm">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
