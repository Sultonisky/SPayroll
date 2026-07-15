@props(['type' => null, 'message' => null])

{{-- Manual Alert via Props --}}
@if ($type && $message)
    @php
        $icon = match ($type) {
            'success' => 'fa-check-circle',
            'danger', 'error' => 'fa-exclamation-circle',
            'warning' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle',
            default => 'fa-info-circle',
        };
        $alertType = match ($type) {
            'success' => 'primary',
            'danger', 'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info',
            default => $type,
        };
    @endphp
    <div class="alert alert-{{ $alertType }} alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas {{ $icon }} fa-lg me-3"></i>
            <div class="pe-4 fw-bold">{{ $message }}</div>
        </div>
        <button type="button" class="btn-close" data-coreui-dismiss="alert" data-bs-dismiss="alert"
            aria-label="Close"></button>
    </div>
@else
    {{-- Automatic Session Alerts --}}
    @if (session('success'))
        <div class="alert alert-primary alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg me-3"></i>
                <div class="pe-4 fw-bold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-coreui-dismiss="alert" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-lg me-3"></i>
                <div class="pe-4 fw-bold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-coreui-dismiss="alert" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                <div class="pe-4 fw-bold">{{ session('warning') }}</div>
            </div>
            <button type="button" class="btn-close" data-coreui-dismiss="alert" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-lg me-3"></i>
                <div class="pe-4 fw-bold">{{ session('info') }}</div>
            </div>
            <button type="button" class="btn-close" data-coreui-dismiss="alert" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-lg me-3"></i>
                <div class="pe-4 fw-bold">
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-coreui-dismiss="alert" data-bs-dismiss="alert"
                aria-label="Close"></button>
        </div>
    @endif
@endif
