@extends('layouts.app')
@section('title', 'Payroll Detail')

@section('contents')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between py-3 gap-3">
                    <h5 class="mb-0 fw-bold text-primary fs-5">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Payroll Details
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['HR', 'manager']))
                            <a href="{{ route('payrolls.edit', $payroll->id) }}"
                                class="btn btn-warning btn-sm rounded-pill px-3 border shadow-sm">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                        @endif
                        <a href="{{ route('payrolls.export', $payroll->id) }}"
                            class="btn btn-outline-primary btn-sm rounded-pill px-3 border shadow-sm">
                            <i class="fas fa-download me-2"></i>Export CSV
                        </a>
                        <a href="{{ route('payrolls.index') }}"
                            class="btn btn-secondary btn-sm rounded-pill px-3 border shadow-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Status + Actions --}}
                    @php
                        $bannerClass = match($payroll->status) {
                            'paid'     => 'alert-primary',
                            'approved' => 'alert-info',
                            default    => 'alert-warning',
                        };
                    @endphp
                    <div class="alert {{ $bannerClass }} d-flex align-items-center justify-content-between mb-4">
                        <span><i class="fas fa-circle me-2"></i><strong>{{ ucfirst($payroll->status) }}</strong></span>
                        <div class="d-flex gap-2">
                            @if ($payroll->isDraft() && (auth()->user()->isAdmin() || auth()->user()->role === 'HR'))
                                <form action="{{ route('payrolls.approve', $payroll->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn bg-primary btn-sm rounded-pill px-3"
                                        onclick="return confirm('Approve this payroll?')">
                                        <i class="fas fa-check me-2"></i>Approve
                                    </button>
                                </form>
                            @endif
                            @if ($payroll->isApproved() && (auth()->user()->isAdmin() || auth()->user()->role === 'HR'))
                                <form action="{{ route('payrolls.mark-paid', $payroll->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"
                                        onclick="return confirm('Mark as paid?')">
                                        <i class="fas fa-money-check-alt me-2"></i>Mark Paid
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Employee</div>
                                    <div class="fs-6 fw-bold text-body">{{ $payroll->employee?->name ?? '-' }}</div>
                                    <small class="text-muted font-monospace">{{ $payroll->employee?->employee_code ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Period</div>
                                    <div class="fs-6 fw-bold text-body">{{ $payroll->monthName() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Pay Date</div>
                                    <div class="fs-6 fw-bold text-body">
                                        {{ $payroll->pay_date?->translatedFormat('d F Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Salary breakdown --}}
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Base Salary</div>
                                    <div class="fs-5 fw-bold text-body">
                                        Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Bonus</div>
                                    <div class="fs-5 fw-bold text-success">
                                        + Rp {{ number_format($payroll->bonus, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm border border-primary">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Total Salary</div>
                                    <div class="fs-4 fw-bold text-primary">
                                        Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($payroll->notes)
                            <div class="col-12">
                                <div class="card border-0 bg-body-tertiary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="text-uppercase small fw-bold text-primary mb-2">Notes</div>
                                        <div class="fs-6 text-body">{{ $payroll->notes }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Created At</div>
                                    <div class="fs-6 fw-bold text-body">
                                        {{ $payroll->created_at->translatedFormat('d F Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 border-0 bg-body-tertiary shadow-sm">
                                <div class="card-body p-3">
                                    <div class="text-uppercase small fw-bold text-primary mb-2">Last Updated</div>
                                    <div class="fs-6 fw-bold text-body">
                                        {{ $payroll->updated_at->translatedFormat('d F Y, H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top text-center text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Information retrieved from system database on {{ now()->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
