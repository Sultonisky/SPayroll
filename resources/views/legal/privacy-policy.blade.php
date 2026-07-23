<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>S-Payroll | Privacy Policy</title>
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

        <h1>Privacy Policy</h1>
        <p class="last-updated">Last updated: {{ date('F d, Y') }}</p>

        <p>
            S-Payroll is an open source, self-hosted payroll system. This Privacy Policy explains how data is handled
            when you use this live demo instance. If you are running your own self-hosted instance, you are responsible
            for your own data practices.
        </p>

        <h2>1. Data We Collect</h2>
        <p>On this demo instance, we may collect:</p>
        <ul>
            <li>Login credentials you use to access the demo (email and password)</li>
            <li>Actions performed within the application (e.g., records created or modified)</li>
            <li>Basic usage logs for security and debugging purposes</li>
        </ul>

        <h2>2. How We Use Your Data</h2>
        <p>Data collected on this demo is used solely to operate and maintain the demo environment. We do not sell,
        share, or use your data for marketing purposes.</p>

        <h2>3. Data Retention</h2>
        <p>Demo data may be reset periodically. Do not store any real or sensitive personal information in this demo instance.</p>

        <h2>4. Self-Hosted Instances</h2>
        <p>If you deploy S-Payroll on your own infrastructure, you are fully responsible for the privacy and security
        of your users' data. Please review the
        <a href="https://github.com/Sultonisky/s-payroll#readme" target="_blank" rel="noopener noreferrer">documentation</a>
        for security best practices.</p>

        <h2>5. Contact</h2>
        <p>For questions about this privacy policy, please open an issue on the
        <a href="https://github.com/Sultonisky/s-payroll" target="_blank" rel="noopener noreferrer">GitHub repository</a>.</p>
    </div>
</body>
</html>
