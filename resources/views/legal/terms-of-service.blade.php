<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>S-Payroll | Terms of Service</title>
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/css/coreui.min.css" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo.svg') }}">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; }
        .legal-container { max-width: 760px; margin: 60px auto; padding: 0 24px 80px; }
        .legal-logo { margin-bottom: 32px; }
        .legal-logo img { height: 40px; }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
        .last-updated { font-size: 13px; color: #64748b; margin-bottom: 40px; }
        h2 { font-size: 17px; font-weight: 700; margin-top: 36px; margin-bottom: 10px; }
        p, li { font-size: 14.5px; line-height: 1.75; color: #334155; }
        ul { padding-left: 20px; }
        a { color: #1e293b; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; margin-bottom: 32px; }
        .back-link:hover { color: #1e293b; }
    </style>
</head>
<body>
    <div class="legal-container">
        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
        <div class="legal-logo">
            <img src="{{ asset('assets/images/logo-brand.svg') }}" alt="S-Payroll">
        </div>

        <h1>Terms of Service</h1>
        <p class="last-updated">Last updated: {{ date('F d, Y') }}</p>

        <p>
            By accessing and using this S-Payroll demo instance, you agree to the following terms.
            S-Payroll is an open source project licensed under the MIT License and is provided "as is",
            without warranty of any kind.
        </p>

        <h2>1. Acceptable Use</h2>
        <p>You agree to use this demo responsibly. You must not:</p>
        <ul>
            <li>Attempt to compromise the security or availability of this service</li>
            <li>Store real personal, financial, or sensitive employee data in the demo</li>
            <li>Use the demo for any unlawful purpose</li>
            <li>Attempt to access accounts or data that do not belong to you</li>
        </ul>

        <h2>2. Demo Environment</h2>
        <p>This is a live demo intended to showcase the features of S-Payroll. Data may be reset at any time
        without prior notice. Do not rely on this demo for production use.</p>

        <h2>3. Open Source License</h2>
        <p>S-Payroll is released under the <a href="https://opensource.org/licenses/MIT" target="_blank" rel="noopener noreferrer">MIT License</a>.
        You are free to use, copy, modify, and distribute the source code subject to the terms of that license.
        The full source code is available on <a href="https://github.com/Sultonisky/s-payroll" target="_blank" rel="noopener noreferrer">GitHub</a>.</p>

        <h2>4. Disclaimer of Warranty</h2>
        <p>This software is provided "as is", without warranty of any kind, express or implied. The authors are not
        liable for any damages arising from the use of this software.</p>

        <h2>5. Changes to These Terms</h2>
        <p>These terms may be updated at any time. Continued use of the demo constitutes acceptance of the updated terms.</p>

        <h2>6. Contact</h2>
        <p>For questions or concerns, please open an issue on the
        <a href="https://github.com/Sultonisky/s-payroll" target="_blank" rel="noopener noreferrer">GitHub repository</a>.</p>
    </div>
</body>
</html>
