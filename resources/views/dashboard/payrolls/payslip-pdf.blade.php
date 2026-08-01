<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payslip - {{ $payroll->employee?->name ?? '-' }} - {{ $payroll->monthName() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 30px 40px;
        }

        /* ── Header ──────────────────────────────── */
        .header {
            background-color: #00F260;
            color: #000;
            padding: 20px 24px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
        }
        .header-inner {
            width: 100%;
        }
        .header-left { float: left; }
        .header-right { float: right; text-align: right; }
        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #000;
        }
        .header p {
            font-size: 11px;
            color: #000;
            opacity: 0.85;
        }
        .clearfix::after { content: ""; display: table; clear: both; }

        /* ── Card body ───────────────────────────── */
        .card-body {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 20px 24px;
            border-radius: 0 0 6px 6px;
        }

        /* ── Section titles ──────────────────────── */
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6c757d;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        /* ── Employee info table ─────────────────── */
        .info-wrap {
            width: 100%;
            margin-bottom: 18px;
        }
        .info-col { width: 50%; vertical-align: top; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td {
            padding: 3px 4px;
            font-size: 11px;
            vertical-align: top;
        }
        .info-label { color: #6c757d; width: 42%; }
        .info-sep   { color: #6c757d; width: 5%; }
        .info-value { font-weight: normal; }
        .info-value.bold { font-weight: bold; }

        /* ── Type badge ──────────────────────────── */
        .type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }

        /* ── Salary table ────────────────────────── */
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 11px;
        }
        .salary-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 6px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #1a1a1a;
        }
        .salary-table td {
            border: 1px solid #dee2e6;
            padding: 7px 10px;
        }
        .salary-table .text-right { text-align: right; }
        .salary-table .text-success { color: #198754; font-weight: 600; }
        .salary-table .text-muted   { color: #6c757d; }
        .salary-table tfoot td,
        .salary-table tfoot th {
            background-color: #e9f0ff;
            border: 1px solid #c5d5fb;
            font-weight: bold;
            font-size: 12px;
            padding: 8px 10px;
        }

        /* ── Summary strip ───────────────────────── */
        .summary-wrap { width: 100%; margin-bottom: 20px; }
        .summary-cell {
            width: 25%;
            vertical-align: top;
            padding: 0 4px;
        }
        .summary-box {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 6px;
            text-align: center;
        }
        .summary-box.green {
            background-color: #00F260;
            border-color: #00F260;
        }
        .summary-label { font-size: 9px; color: #6c757d; margin-bottom: 3px; }
        .summary-value { font-size: 12px; font-weight: bold; }
        .summary-box.green .summary-label { color: #000; }
        .summary-box.green .summary-value { color: #000; }

        /* ── Notes ───────────────────────────────── */
        .notes-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 16px;
        }

        /* ── Signature ───────────────────────────── */
        .sig-wrap { width: 100%; border-top: 1px solid #dee2e6; padding-top: 18px; margin-top: 10px; }
        .sig-cell { width: 50%; text-align: center; vertical-align: top; padding: 0 20px; }
        .sig-role  { font-size: 10px; color: #6c757d; margin-bottom: 8px; }
        .sig-space { height: 52px; } /* matches logo height in HR column */
        .sig-line  { border-bottom: 1px solid #1a1a1a; margin: 0 30px 6px; }
        .sig-name  { font-size: 11px; font-weight: bold; }
        .sig-code  { font-size: 10px; color: #6c757d; }

        /* ── Doc footer ──────────────────────────── */
        .doc-footer {
            text-align: center;
            font-size: 9px;
            color: #adb5bd;
            border-top: 1px solid #f0f0f0;
            margin-top: 20px;
            padding-top: 10px;
        }

        /* ── Watermark ───────────────────────────── */
        .watermark {
            position: fixed;
            top: 38%;
            left: 50%;
            transform: translateX(-50%) translateY(-50%) rotate(-35deg);
            opacity: 0.055;
            z-index: 0;
            pointer-events: none;
        }
        .watermark img {
            width: 580px;
        }

        /* ── Doc ID badge ────────────────────────── */
        .doc-id-badge {
            display: inline-block;
            background-color: rgba(0,0,0,0.12);
            border: 1px solid rgba(0,0,0,0.2);
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 9px;
            color: #000;
            letter-spacing: 0.5px;
            font-family: monospace;
        }

        /* ── Footer bar ──────────────────────────── */
        .footer-bar {
            margin-top: 18px;
            border-top: 2px solid #dee2e6;
            padding-top: 10px;
        }
        .footer-bar table { width: 100%; }
        .footer-left  { font-size: 9px; color: #6c757d; vertical-align: middle; }
        .footer-right { font-size: 9px; color: #6c757d; text-align: right; vertical-align: middle; }
        .footer-verify-url { font-size: 9px; color: #6c757d; text-align: center; margin-top: 5px; }
    </style>
</head>
@php
    // Document ID: deterministik dari data payroll — tidak bisa dipalsukan tanpa tahu data aslinya
    $docHash = strtoupper(substr(hash('sha256',
        $payroll->id . $payroll->employee_id . $payroll->year . $payroll->month .
        $payroll->total_salary . $payroll->pay_date
    ), 0, 12));
    $docId = 'SCR-' . str_pad($payroll->id, 5, '0', STR_PAD_LEFT) . '-' . $docHash;
@endphp
<body>

    {{-- ── WATERMARK (rendered behind all content) ───────────── --}}
    <div class="watermark">
        <img src="{{ public_path('assets/images/logo-scroll.png') }}" alt="">
    </div>

    {{-- ── HEADER ────────────────────────────────────────────── --}}
    <div class="header">
        <div class="header-inner clearfix">
            <div class="header-left">
                <h2>Scroll | Payslip</h2>
                <p>Periode: <strong>{{ $payroll->monthName() }}</strong></p>
            </div>
            <div class="header-right">
                <div class="doc-id-badge">{{ $docId }}</div>
                <br>
                <span style="font-size:10px; color:#000; opacity:0.8;">
                    <strong>{{ $payroll->pay_date?->translatedFormat('d F Y, H:i') ?? '-' }}</strong>
                </span>
            </div>
        </div>
    </div>

    {{-- ── CARD BODY ──────────────────────────────────────────── --}}
    <div class="card-body">

        {{-- Employee Info --}}
        <div class="section-title">Employee Information</div>
        <table class="info-wrap">
            <tr>
                {{-- Left col --}}
                <td class="info-col" style="padding-right: 10px;">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Name</td>
                            <td class="info-sep">:</td>
                            <td class="info-value bold">{{ $payroll->employee?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Employee Code</td>
                            <td class="info-sep">:</td>
                            <td class="info-value">{{ $payroll->employee?->employee_code ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">NIK</td>
                            <td class="info-sep">:</td>
                            <td class="info-value">{{ $payroll->employee?->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Type</td>
                            <td class="info-sep">:</td>
                            <td class="">
                                @php $type = $payroll->employee?->employee_type ?? '-'; @endphp
                                <span class="{{ $type === 'fulltime' ? 'type-fulltime' : 'type-internship' }}">
                                    {{ ucfirst($type) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                {{-- Right col --}}
                <td class="info-col" style="padding-left: 10px;">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Department</td>
                            <td class="info-sep">:</td>
                            <td class="info-value">{{ $payroll->employee?->department?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Position</td>
                            <td class="info-sep">:</td>
                            <td class="info-value">{{ $payroll->employee?->position?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Bank</td>
                            <td class="info-sep">:</td>
                            <td class="info-value">{{ $payroll->employee?->bank_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Account No.</td>
                            <td class="info-sep">:</td>
                            <td class="info-value">{{ $payroll->employee?->bank_account_number ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Salary Breakdown --}}
        <div class="section-title">Salary Details</div>
        <table class="salary-table">
            <thead>
                <tr>
                    <th>Component</th>
                    <th class="text-right" style="width: 35%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Base Salary</td>
                    <td class="text-right">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                </tr>

                @if ($bonuses->isNotEmpty())
                    @foreach ($bonuses as $bonus)
                        <tr>
                            <td>
                                Bonus &mdash; {{ $bonus->type ? ucfirst($bonus->type) : 'Bonus' }}
                                @if ($bonus->description)
                                    <span class="text-muted">({{ $bonus->description }})</span>
                                @endif
                            </td>
                            <td class="text-right text-success">+ Rp {{ number_format($bonus->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @elseif ($payroll->bonus > 0)
                    <tr>
                        <td>Bonus</td>
                        <td class="text-right text-success">+ Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="text-muted">Bonus</td>
                        <td class="text-right text-muted">&mdash;</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Salary Received</td>
                    <td class="text-right">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Notes --}}
        @if ($payroll->notes)
            <div class="section-title">Notes</div>
            <div class="notes-box">{{ $payroll->notes }}</div>
        @endif

        {{-- Summary Strip --}}
        <table class="summary-wrap">
            <tr>
                <td class="summary-cell">
                    <div class="summary-box">
                        <div class="summary-label">Payroll ID</div>
                        <div class="summary-value">#{{ $payroll->id }}</div>
                    </div>
                </td>
                <td class="summary-cell">
                    <div class="summary-box">
                        <div class="summary-label">Period</div>
                        <div class="summary-value">{{ \Carbon\Carbon::create($payroll->year, $payroll->month)->format('m/Y') }}</div>
                    </div>
                </td>
                <td class="summary-cell">
                    <div class="summary-box">
                        <div class="summary-label">Payment Date</div>
                        <div class="summary-value">{{ $payroll->pay_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </td>
                <td class="summary-cell">
                    <div class="summary-box green">
                        <div class="summary-label">Status</div>
                        <div class="summary-value">Paid</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Signature --}}
        <table class="sig-wrap">
            <tr>
                {{-- Karyawan: baris kosong untuk tanda tangan manual --}}
                <td class="sig-cell">
                    <div class="sig-role">Karyawan / Employee</div>
                    <div class="sig-space"></div>
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $payroll->employee?->name ?? '-' }}</div>
                    <div class="sig-code">{{ $payroll->employee?->employee_code ?? '' }}</div>
                </td>
                {{-- HR/Finance: logo sebagai cap perusahaan --}}
                <td class="sig-cell">
                    <div class="sig-role">HR / Finance</div>
                    <div style="text-align: center; margin-bottom: 4px;">
                        <img src="{{ public_path('assets/images/logo-scroll.png') }}"
                             alt="Scroll"
                             style="height: 44px; opacity: 0.85;">
                    </div>
                    <div class="sig-line"></div>
                    <div class="sig-name">Authorized Signatory</div>
                    <div class="sig-code">HR / Finance Department</div>
                </td>
            </tr>
        </table>

    </div>{{-- /card-body --}}

    {{-- ── FOOTER BAR ─────────────────────────────────────────── --}}
    <div class="footer-bar">
        <table>
            <tr>
                <td class="footer-left">
                    <img src="{{ public_path('assets/images/logo-scroll.png') }}"
                         alt="Scroll" style="height: 14px; vertical-align: middle; margin-right: 5px; opacity: 0.6;">
                    Generated by <strong>Scroll Payroll System</strong>
                    &nbsp;&bull;&nbsp; {{ now()->translatedFormat('d F Y, H:i') }}
                </td>
                <td class="footer-right">
                    Doc ID: <strong>{{ $docId }}</strong>
                </td>
            </tr>
        </table>
        <div class="footer-verify-url">
            Verify this document at: <strong>{{ url('/verify') }}</strong>
            &nbsp;&bull;&nbsp; Enter Doc ID: <strong>{{ $docId }}</strong>
        </div>
    </div>

</body>
</html>
