@extends('layouts.app')
@section('title', 'Payslip – ' . ($payroll->employee?->name ?? '-') . ' – ' . $payroll->monthName())

@section('contents')
<div class="row justify-content-center">
    <div class="col-12 col-lg-9 col-xl-8">

        {{-- Action Bar (hidden on print) --}}
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="{{ route('payrolls.payslip') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Payslips
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-print me-2"></i>Print / Save PDF
            </button>
        </div>

        {{-- ── PAYSLIP CARD ─────────────────────────────────────────── --}}
        <div class="card shadow-sm payslip-card" id="payslip-print-area">

            {{-- Header --}}
            <div class="card-header bg-primary text-white py-4 px-4 px-md-5">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h4 class="mb-1 fw-bold text-black">
                            <i class="fas fa-file-invoice me-2"></i>Payslip / Slip Gaji
                        </h4>
                        <p class="mb-0 text-black opacity-75 small">
                            Periode: <strong class="text-black">{{ $payroll->monthName() }}</strong>
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success text-white rounded-pill px-3 py-2 fs-6 mb-1">
                            <i class="fas fa-check-circle me-1"></i>Paid
                        </span>
                        <div class="small text-black opacity-75 mt-1">
                            Tanggal Bayar: <strong class="text-black">{{ $payroll->pay_date?->translatedFormat('d F Y') ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-4 px-md-5 py-4">

                {{-- ── Employee Info ─────────────────────────── --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3 border-bottom pb-2">
                            <i class="fas fa-user me-2"></i>Informasi Karyawan
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted small fw-semibold" style="width:45%">Nama</td>
                                    <td class="text-muted small">:</td>
                                    <td class="fw-bold">{{ $payroll->employee?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-semibold">Kode Karyawan</td>
                                    <td class="text-muted small">:</td>
                                    <td>{{ $payroll->employee?->employee_code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-semibold">NIK</td>
                                    <td class="text-muted small">:</td>
                                    <td>{{ $payroll->employee?->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-semibold">Tipe</td>
                                    <td class="text-muted small">:</td>
                                    <td>
                                        @php
                                            $type = $payroll->employee?->employee_type;
                                            $typeColor = $type === 'fulltime' ? 'bg-primary' : 'bg-warning text-dark';
                                        @endphp
                                        <span class="badge {{ $typeColor }} rounded-pill">
                                            {{ ucfirst($type ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted small fw-semibold" style="width:45%">Departemen</td>
                                    <td class="text-muted small">:</td>
                                    <td>{{ $payroll->employee?->department?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-semibold">Posisi</td>
                                    <td class="text-muted small">:</td>
                                    <td>{{ $payroll->employee?->position?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-semibold">Bank</td>
                                    <td class="text-muted small">:</td>
                                    <td>{{ $payroll->employee?->bank_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-semibold">No. Rekening</td>
                                    <td class="text-muted small">:</td>
                                    <td>{{ $payroll->employee?->bank_account_number ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ── Salary Breakdown ──────────────────────── --}}
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold small mb-3 border-bottom pb-2">
                        <i class="fas fa-money-bill-wave me-2"></i>Rincian Gaji
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-end">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Base Salary --}}
                                <tr>
                                    <td>
                                        <i class="fas fa-wallet me-2 text-primary"></i>
                                        Gaji Pokok
                                    </td>
                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}
                                    </td>
                                </tr>

                                {{-- Bonus breakdown --}}
                                @if ($bonuses->isNotEmpty())
                                    @foreach ($bonuses as $bonus)
                                        <tr>
                                            <td>
                                                <i class="fas fa-gift me-2 text-success"></i>
                                                Bonus – {{ $bonus->type ? ucfirst($bonus->type) : 'Bonus' }}
                                                @if ($bonus->description)
                                                    <span class="text-muted small">({{ $bonus->description }})</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-success fw-semibold">
                                                + Rp {{ number_format($bonus->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif ($payroll->bonus > 0)
                                    {{-- Fallback if bonuses not resolved individually --}}
                                    <tr>
                                        <td><i class="fas fa-gift me-2 text-success"></i>Bonus</td>
                                        <td class="text-end text-success fw-semibold">
                                            + Rp {{ number_format($payroll->bonus, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="text-muted"><i class="fas fa-gift me-2"></i>Bonus</td>
                                        <td class="text-end text-muted">—</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <th class="fw-bold text-uppercase">
                                        <i class="fas fa-coins me-2"></i>Total Gaji Diterima
                                    </th>
                                    <th class="text-end fw-bold fs-5">
                                        Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- ── Notes ────────────────────────────────── --}}
                @if ($payroll->notes)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2 border-bottom pb-2">
                            <i class="fas fa-sticky-note me-2"></i>Catatan
                        </h6>
                        <p class="text-muted mb-0 small">{{ $payroll->notes }}</p>
                    </div>
                @endif

                {{-- ── Summary Strip ───────────────────────── --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="rounded-3 bg-light p-3 text-center border">
                            <div class="text-muted small mb-1">Payroll ID</div>
                            <div class="fw-bold">#{{ $payroll->id }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="rounded-3 bg-light p-3 text-center border">
                            <div class="text-muted small mb-1">Periode</div>
                            <div class="fw-bold">{{ \Carbon\Carbon::create($payroll->year, $payroll->month)->format('m/Y') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="rounded-3 bg-light p-3 text-center border">
                            <div class="text-muted small mb-1">Tgl Bayar</div>
                            <div class="fw-bold">{{ $payroll->pay_date?->format('d/m/Y') ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="rounded-3 bg-success p-3 text-center">
                            <div class="text-white small mb-1 opacity-75">Status</div>
                            <div class="fw-bold text-white">Paid</div>
                        </div>
                    </div>
                </div>

                {{-- ── Signature Area ───────────────────────── --}}
                <div class="row mt-5 pt-3 border-top">
                    <div class="col-6 text-center">
                        <p class="small text-muted mb-5">Karyawan</p>
                        <div class="border-bottom mx-4 mb-1"></div>
                        <p class="small fw-bold mb-0">{{ $payroll->employee?->name ?? '-' }}</p>
                        <p class="small text-muted">{{ $payroll->employee?->employee_code ?? '' }}</p>
                    </div>
                    <div class="col-6 text-center">
                        <p class="small text-muted mb-5">HR / Finance</p>
                        <div class="border-bottom mx-4 mb-1"></div>
                        <p class="small fw-bold mb-0">Authorized Signatory</p>
                        <p class="small text-muted">HR / Finance Department</p>
                    </div>
                </div>

            </div>{{-- /card-body --}}

            {{-- Footer --}}
            <div class="card-footer bg-light text-center text-muted small py-3">
                Dokumen ini digenerate secara otomatis oleh sistem S-Payroll.
                Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}.
            </div>
        </div>
        {{-- /payslip-card --}}

    </div>
</div>
@endsection

@push('styles')
<style>
    .payslip-card {
        border: 1px solid #dee2e6;
    }

    @media print {
        /* Hide everything except the payslip */
        body * { visibility: hidden; }
        #payslip-print-area,
        #payslip-print-area * { visibility: visible; }
        #payslip-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
        .no-print { display: none !important; }
        .sidebar, .navbar, .footer, .breadcrumb { display: none !important; }

        /* Ensure table borders print */
        .table-bordered td,
        .table-bordered th {
            border: 1px solid #dee2e6 !important;
        }
        .table-primary th {
            background-color: #cfe2ff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .bg-primary {
            background-color: #0d6efd !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .bg-success {
            background-color: #198754 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush

@push('scripts')
    @if(request('print'))
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 400);
        });
    </script>
    @endif
@endpush
