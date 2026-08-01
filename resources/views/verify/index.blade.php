<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payslip Verification — Scroll</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/css/coreui.min.css" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <style>
        body {
            background: #f4f6fb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .verify-card {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .verify-header {
            background-color: #00F260;
            padding: 28px 32px 20px;
            text-align: center;
        }
        .verify-header img { height: 38px; margin-bottom: 10px; }
        .verify-header h1 { font-size: 1.25rem; font-weight: 700; color: #000; margin: 0; }
        .verify-header p  { font-size: 0.82rem; color: #000; opacity: 0.7; margin: 4px 0 0; }
        .verify-body { padding: 28px 32px; }

        /* Input group */
        .input-doc-id {
            font-family: monospace;
            font-size: 0.95rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Result: VALID */
        .result-valid {
            border: 2px solid #00F260;
            border-radius: 8px;
            padding: 20px 22px;
            background: #f0fff8;
        }
        .result-valid .result-icon { color: #00a854; font-size: 2.2rem; }
        .result-valid .result-title { color: #00a854; font-weight: 700; font-size: 1rem; }

        /* Result: INVALID */
        .result-invalid {
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px 22px;
            background: #fff5f5;
        }
        .result-invalid .result-icon { color: #dc3545; font-size: 2.2rem; }
        .result-invalid .result-title { color: #dc3545; font-weight: 700; font-size: 1rem; }

        /* Info rows */
        .info-row { display: flex; padding: 5px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.85rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6c757d; width: 46%; flex-shrink: 0; }
        .info-value { font-weight: 600; color: #1a1a1a; }
        .info-value.mono { font-family: monospace; font-size: 0.78rem; color: #555; }

        /* Doc ID chip */
        .doc-id-chip {
            display: inline-block;
            background: rgba(0,0,0,0.07);
            border: 1px solid rgba(0,0,0,0.12);
            border-radius: 4px;
            padding: 2px 8px;
            font-family: monospace;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
        }

        .verify-footer {
            text-align: center;
            padding: 14px 32px 20px;
            font-size: 0.75rem;
            color: #adb5bd;
            border-top: 1px solid #f0f0f0;
        }
        .verify-footer a { color: #adb5bd; text-decoration: none; }
        .verify-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="verify-card">

    {{-- Header --}}
    <div class="verify-header">
        <img src="{{ asset('assets/images/logo-scroll.png') }}" alt="Scroll">
        <h1><i class="fas fa-shield-alt me-2"></i>Payslip Verification</h1>
        <p>Verify the authenticity of a payslip document issued by Scroll Payroll System.</p>
    </div>

    <div class="verify-body">

        {{-- Form --}}
        <form method="POST" action="{{ route('verify.payslip') }}" autocomplete="off">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing:.5px">
                    <i class="fas fa-fingerprint me-1"></i>Document ID (Doc ID)
                </label>
                <input
                    type="text"
                    name="doc_id"
                    class="form-control input-doc-id @error('doc_id') is-invalid @enderror"
                    placeholder="SCR-00042-A3F9C1D82E4B"
                    value="{{ old('doc_id') }}"
                    maxlength="22"
                    autofocus
                >
                @error('doc_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text text-muted" style="font-size:0.78rem;">
                    Doc ID can be found in the payslip header and footer.
                </div>
            </div>

            <button type="submit" class="btn w-100 fw-bold" style="background:#00F260; color:#000;">
                <i class="fas fa-search me-2"></i>Verify Document
            </button>
        </form>

        {{-- Result --}}
        @if (session('result'))
            @php $r = session('result'); @endphp
            <div class="mt-4 {{ $r['valid'] ? 'result-valid' : 'result-invalid' }}">
                <div class="text-center mb-3">
                    <div class="result-icon">
                        <i class="fas {{ $r['valid'] ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    </div>
                    <div class="result-title mt-1">
                        {{ $r['valid'] ? 'Document is VALID' : 'Document is INVALID' }}
                    </div>
                    <div class="mt-1">
                        <span class="doc-id-chip">{{ $r['doc_id'] }}</span>
                    </div>
                </div>

                @if ($r['valid'])
                    <div class="mt-3">
                        <div class="info-row">
                            <span class="info-label">Employee Name</span>
                            <span class="info-value">{{ $r['employee'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Employee Code</span>
                            <span class="info-value">{{ $r['employee_code'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Department</span>
                            <span class="info-value">{{ $r['department'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Position</span>
                            <span class="info-value">{{ $r['position'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Period</span>
                            <span class="info-value">{{ $r['period'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Date</span>
                            <span class="info-value">{{ $r['pay_date'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Salary</span>
                            <span class="info-value">{{ $r['total_salary'] }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge bg-success rounded-pill px-3">{{ $r['status'] }}</span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Verified At</span>
                            <span class="info-value mono">{{ $r['verified_at'] }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-center text-danger small mb-0 mt-2">
                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $r['message'] }}
                    </p>
                @endif
            </div>
        @endif

    </div>{{-- /verify-body --}}

    <div class="verify-footer">
        This page is publicly accessible - no login required.<br>
        &copy; {{ date('Y') }} <a href="{{ url('/') }}">Scroll Payroll System</a>
        &nbsp;&bull;&nbsp;
        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
    </div>

</div>

</body>
</html>
