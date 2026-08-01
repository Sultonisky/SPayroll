<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Scroll Terms of Service - terms governing use of the official Scroll demo environment.">
    <title>Scroll | Terms of Service</title>
    <link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.0.0/dist/css/coreui.min.css" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; }
        .legal-container { max-width: 780px; margin: 0 auto; padding: 48px 24px 96px; }
        .legal-logo { margin-bottom: 28px; }
        .legal-logo img { height: 38px; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #64748b; text-decoration: none; margin-bottom: 28px; transition: color .15s; }
        .back-link:hover { color: #1e293b; }
        h1 { font-size: 26px; font-weight: 700; margin: 0 0 6px; line-height: 1.3; }
        .meta-row { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; margin-bottom: 32px; }
        .badge-mit { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; border-radius: 20px; padding: 3px 10px; }
        .badge-uu { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 20px; padding: 3px 10px; }
        .last-updated { font-size: 13px; color: #64748b; }
        .intro-box { background: #f1f5f9; border-left: 3px solid #3b82f6; border-radius: 6px; padding: 14px 18px; margin-bottom: 24px; }
        .intro-box p { margin: 0; font-size: 14px; line-height: 1.7; color: #334155; }
        .warn-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 12px 16px; margin: 14px 0; }
        .warn-box p { margin: 0; font-size: 14px; color: #9a3412; }
        .highlight-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 16px; margin: 14px 0; }
        .highlight-box p { margin: 0; font-size: 14px; }
        .info-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 6px; padding: 12px 16px; margin: 14px 0; }
        .info-box p { margin: 0; font-size: 14px; color: #0c4a6e; }
        .toc { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 22px; margin-bottom: 40px; }
        .toc h3 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin: 0 0 10px; }
        .toc ol { margin: 0; padding-left: 20px; }
        .toc li { font-size: 13.5px; line-height: 2; }
        .toc a { color: #1e293b; text-decoration: none; }
        .toc a:hover { color: #3b82f6; text-decoration: underline; }
        h2 { font-size: 16px; font-weight: 700; margin: 40px 0 10px; padding-top: 8px; border-top: 1px solid #e2e8f0; color: #0f172a; scroll-margin-top: 20px; }
        h3 { font-size: 14px; font-weight: 700; margin: 20px 0 8px; color: #1e293b; }
        p { font-size: 14.5px; line-height: 1.8; color: #334155; margin: 0 0 12px; }
        ul, ol { padding-left: 22px; margin: 0 0 14px; }
        li { font-size: 14.5px; line-height: 1.8; color: #334155; }
        code { font-size: 13px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 1px 6px; color: #0f172a; font-family: 'Consolas', 'Menlo', monospace; }
        a { color: #2563eb; }
        a:hover { color: #1d4ed8; }
        .contact-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px 24px; margin-top: 12px; }
        .contact-card p { margin: 0 0 6px; }
        .contact-card p:last-child { margin: 0; }
        .footer-legal { margin-top: 56px; padding-top: 24px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #94a3b8; text-align: center; }
        .footer-legal a { color: #64748b; text-decoration: none; }
        .footer-legal a:hover { color: #1e293b; }
        @media (max-width: 600px) { .legal-container { padding: 32px 16px 72px; } h1 { font-size: 22px; } }
    </style>
</head>
<body>
<div class="legal-container">

    <a href="{{ route('login') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Login
    </a>

    <div class="legal-logo">
        <img src="{{ asset('assets/images/logo-scroll.png') }}" alt="Scroll">
    </div>

    <h1>Terms of Service</h1>
    <div class="meta-row">
        <span class="last-updated">Last Updated: {{ date('F d, Y') }}</span>
        <span class="badge-mit"><i class="fab fa-osi"></i> MIT License</span>
        <span class="badge-uu"><i class="fas fa-balance-scale"></i> UU ITE &amp; UU PDP</span>
    </div>

    <div class="intro-box">
        <p>
            These Terms of Service govern your access to and use of the <strong>official Scroll live demo</strong>
            operated by the Scroll project maintainers. Scroll is an open-source, self-hosted payroll management
            system distributed under the <strong>MIT License</strong>. By accessing the demo, you agree to these terms.
        </p>
    </div>

    <div class="warn-box">
        <p>
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Self-hosted instances:</strong> These Terms of Service apply only to the official demo.
            Organizations operating their own Scroll installation are independently responsible for establishing
            their own terms, policies, and legal compliance.
        </p>
    </div>

    <!-- Table of Contents -->
    <div class="toc">
        <h3>Contents</h3>
        <ol>
            <li><a href="#scope">Scope &amp; Acceptance</a></li>
            <li><a href="#demo">The Official Demo Environment</a></li>
            <li><a href="#acceptable-use">Acceptable Use</a></li>
            <li><a href="#prohibited">Prohibited Conduct</a></li>
            <li><a href="#open-source-license">Open Source License (MIT)</a></li>
            <li><a href="#self-hosted-use">Self-Hosted Use &amp; Operator Responsibilities</a></li>
            <li><a href="#intellectual-property">Intellectual Property</a></li>
            <li><a href="#disclaimer">Disclaimer of Warranties</a></li>
            <li><a href="#liability">Limitation of Liability</a></li>
            <li><a href="#indemnification">Indemnification</a></li>
            <li><a href="#termination">Termination &amp; Access Revocation</a></li>
            <li><a href="#privacy">Privacy</a></li>
            <li><a href="#changes">Changes to These Terms</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#applicable-law">Applicable Law &amp; Dispute Resolution</a></li>
        </ol>
    </div>

    <!-- Section 1 -->
    <h2 id="scope">1. Scope &amp; Acceptance</h2>
    <p>
        These Terms of Service ("Terms") constitute a legally binding agreement between you ("User") and the
        Scroll project maintainers regarding your access to and use of the official Scroll demo environment
        available at this URL.
    </p>
    <p>
        By accessing or using the official demo, you confirm that you have read, understood, and agree to be
        bound by these Terms, as well as the <a href="{{ route('privacy-policy') }}">Privacy Policy</a>.
        If you do not agree, do not access or use the demo.
    </p>
    <p>
        These Terms are governed by the laws of the <strong>Republic of Indonesia</strong>, including
        Undang-Undang No. 11 Tahun 2008 jo. No. 19 Tahun 2016 tentang Informasi dan Transaksi Elektronik
        (UU ITE) and Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).
    </p>

    <!-- Section 2 -->
    <h2 id="demo">2. The Official Demo Environment</h2>
    <p>
        The official Scroll demo is a publicly accessible, live environment provided solely for
        <strong>demonstration, evaluation, and educational purposes</strong>. It showcases the features of the
        Scroll application, including employee management, payroll generation, bonus management, and
        role-based access control.
    </p>
    <ul>
        <li>The demo may contain pre-seeded, fictional employee and payroll data for illustration purposes.</li>
        <li>The demo environment may be <strong>reset, modified, or taken offline at any time</strong> without prior notice.</li>
        <li>The demo is <strong>not intended for production use</strong>. Do not use it to process real payroll or store real organizational data.</li>
        <li>No service level agreement (SLA) or uptime guarantee applies to the demo environment.</li>
        <li>Access to the demo may be restricted, rate-limited, or revoked at the discretion of the project maintainers.</li>
    </ul>

    <!-- Section 3 -->
    <h2 id="acceptable-use">3. Acceptable Use</h2>
    <p>
        You may access and use the official Scroll demo only for lawful, legitimate purposes consistent with
        these Terms. Acceptable uses include:
    </p>
    <ul>
        <li>Evaluating whether Scroll meets your organization's requirements before self-hosting.</li>
        <li>Exploring application features, workflows, and user interfaces.</li>
        <li>Educational or personal learning purposes related to payroll system design.</li>
        <li>Demonstrating the software to colleagues or stakeholders using fictional data.</li>
        <li>Contributing to the project by testing features and reporting bugs through the GitHub repository.</li>
    </ul>
    <div class="warn-box">
        <p>
            <i class="fas fa-exclamation-triangle me-1"></i>
            You must use <strong>only fictional or non-sensitive information</strong> when interacting with the demo.
            Do not enter real employee names, NIK, NPWP, salary data, bank account numbers, or any other
            personal or confidential information.
        </p>
    </div>

    <!-- Section 4 -->
    <h2 id="prohibited">4. Prohibited Conduct</h2>
    <p>
        When using the official Scroll demo, you must not engage in any of the following:
    </p>

    <h3>4.1 Security &amp; System Integrity</h3>
    <ul>
        <li>Attempt to gain unauthorized access to any account, system, or data beyond what is provided to you.</li>
        <li>Probe, scan, or test the vulnerability of the demo system without prior written authorization.</li>
        <li>Attempt to disrupt, degrade, or impair the availability of the demo (e.g., denial-of-service attacks).</li>
        <li>Inject malicious code, scripts, or payloads into the application.</li>
        <li>Attempt to circumvent authentication, role-based access controls, or other security mechanisms.</li>
        <li>Reverse engineer the application for purposes beyond what is freely available in the public source code.</li>
    </ul>

    <h3>4.2 Data &amp; Privacy</h3>
    <ul>
        <li>Enter, upload, or process real personal data, employee records, salary information, national identity numbers (NIK), tax identification numbers (NPWP), or bank account details.</li>
        <li>Attempt to access, copy, or harvest data belonging to other users of the demo.</li>
        <li>Use the demo to process personal data on behalf of a third party without a lawful basis under UU PDP.</li>
    </ul>

    <h3>4.3 Unlawful &amp; Abusive Conduct</h3>
    <ul>
        <li>Use the demo for any purpose that violates applicable Indonesian law, including UU ITE, UU PDP, or other applicable regulations.</li>
        <li>Use the demo to facilitate fraud, deception, money laundering, or any other illegal activity.</li>
        <li>Impersonate any person or entity, or misrepresent your affiliation with any person or organization.</li>
        <li>Use automated tools, bots, or scripts to access the demo at a rate that unreasonably burdens the system.</li>
        <li>Use the demo for commercial purposes that conflict with the MIT License terms.</li>
    </ul>
    <div class="info-box">
        <p>
            <i class="fas fa-info-circle me-1"></i>
            Violations of these prohibitions may result in immediate revocation of demo access and, where applicable
            under Indonesian law (including UU ITE), may constitute a civil or criminal offense.
        </p>
    </div>

    <!-- Section 5 -->
    <h2 id="open-source-license">5. Open Source License (MIT)</h2>
    <p>
        Scroll is distributed as free, open-source software under the
        <a href="https://opensource.org/licenses/MIT" target="_blank" rel="noopener noreferrer">MIT License</a>.
        The full source code is publicly available on
        <a href="https://github.com/Sultonisky/Scroll" target="_blank" rel="noopener noreferrer">GitHub</a>.
    </p>
    <p>Under the MIT License, you are free to:</p>
    <ul>
        <li><strong>Use</strong> - run Scroll for any purpose, including commercial use.</li>
        <li><strong>Copy</strong> - reproduce the source code and software.</li>
        <li><strong>Modify</strong> - adapt the software to meet your organization's needs.</li>
        <li><strong>Distribute</strong> - share the original or modified software with others.</li>
        <li><strong>Sublicense</strong> - incorporate it into larger projects or products.</li>
    </ul>
    <p>Subject to the following conditions:</p>
    <ul>
        <li>The original copyright notice and the MIT License text must be retained in all copies or substantial portions of the software.</li>
        <li>Attribution to the original author is appreciated but not required beyond retaining the copyright notice.</li>
    </ul>
    <div class="highlight-box">
        <p>
            <i class="fas fa-balance-scale me-1" style="color: #92400e;"></i>
            <strong>The MIT License grants rights to the software itself - not to the official demo service.</strong>
            These Terms of Service govern your use of the demo. Your use of the source code to build or run your
            own installation is governed solely by the MIT License.
        </p>
    </div>

    <!-- Section 6 -->
    <h2 id="self-hosted-use">6. Self-Hosted Use &amp; Operator Responsibilities</h2>
    <p>
        Organizations that download, install, and operate Scroll on their own infrastructure
        ("Operators") do so independently and are solely responsible for:
    </p>

    <h3>6.1 Legal &amp; Regulatory Compliance</h3>
    <ul>
        <li>Complying with all applicable laws and regulations, including UU PDP, UU ITE, Indonesian labor law (UU Ketenagakerjaan), tax regulations (PPh 21, BPJS), and any other applicable sector-specific rules.</li>
        <li>Establishing a lawful basis for processing employee personal data under UU PDP.</li>
        <li>Issuing appropriate privacy notices to employees whose data is processed in the system.</li>
        <li>Appointing a Data Protection Officer (<em>Petugas Pelindungan Data Pribadi</em>) where required by applicable law.</li>
    </ul>

    <h3>6.2 Infrastructure &amp; Security</h3>
    <ul>
        <li>Securing the server, database, and network infrastructure on which Scroll is deployed.</li>
        <li>Setting <code>APP_ENV=production</code> and <code>APP_DEBUG=false</code> in all production environments.</li>
        <li>Enforcing HTTPS for all access to the application.</li>
        <li>Rotating the <code>APP_KEY</code> and changing default seeded credentials immediately after first deployment.</li>
        <li>Keeping PHP, Laravel, and all dependencies up to date with security patches.</li>
        <li>Managing access controls, authentication, and user roles appropriate to their organizational context.</li>
    </ul>

    <h3>6.3 Data Accuracy &amp; Payroll</h3>
    <ul>
        <li>Verifying the accuracy of all payroll calculations, tax deductions (PPh 21), and BPJS contribution figures produced by the system before use.</li>
        <li>Scroll does <strong>not</strong> currently implement automated PPh 21 tax calculation. Operators are responsible for ensuring tax compliance through additional processes or integrations.</li>
        <li>Ensuring payroll data is accurate, complete, and authorized before disbursement to employees.</li>
    </ul>

    <div class="highlight-box">
        <p>
            <i class="fas fa-info-circle me-1" style="color: #92400e;"></i>
            The Scroll project maintainers have no access to, and no responsibility for, data stored in
            independently operated self-hosted instances. All operational, legal, and security responsibilities
            rest entirely with the Operator.
        </p>
    </div>

    <!-- Section 7 -->
    <h2 id="intellectual-property">7. Intellectual Property</h2>
    <p>
        The Scroll source code, documentation, and associated materials are the intellectual property of
        the Scroll project maintainers and contributors, and are made available under the MIT License as
        described in Section 5.
    </p>
    <p>
        The Scroll name, logo, and branding are associated with the project. Use of the Scroll name or
        logo to represent a modified or forked version of the software should clearly indicate that it is a
        derivative work and is not the official Scroll project, to avoid confusion.
    </p>
    <p>
        Third-party components used in Scroll (including Laravel, CoreUI, Maatwebsite Excel, Intervention
        Image, Font Awesome, and jQuery) are subject to their respective licenses. Use of Scroll does not
        grant any rights to those third-party trademarks or intellectual property beyond what their respective
        licenses permit.
    </p>

    <!-- Section 8 -->
    <h2 id="disclaimer">8. Disclaimer of Warranties</h2>
    <p>
        The Scroll software and the official demo are provided <strong>"as is"</strong> and
        <strong>"as available"</strong>, without warranty of any kind, express or implied.
    </p>
    <p>To the fullest extent permitted by applicable law, the Scroll project maintainers expressly disclaim:</p>
    <ul>
        <li>Any implied warranty of merchantability, fitness for a particular purpose, or non-infringement.</li>
        <li>Any warranty that the software will be error-free, uninterrupted, secure, or free of bugs or vulnerabilities.</li>
        <li>Any warranty regarding the accuracy, completeness, or reliability of payroll calculations, tax figures, or any output produced by the system.</li>
        <li>Any warranty that the software is compliant with any specific legal or regulatory requirement in any jurisdiction.</li>
    </ul>
    <div class="info-box">
        <p>
            <i class="fas fa-info-circle me-1"></i>
            <strong>Important for Operators:</strong> Scroll is a tool to assist payroll processing. It is not a
            certified accounting system or licensed tax software. All payroll outputs must be independently verified
            by qualified personnel before disbursement or regulatory submission.
        </p>
    </div>

    <!-- Section 9 -->
    <h2 id="liability">9. Limitation of Liability</h2>
    <p>
        To the maximum extent permitted by applicable Indonesian law, the Scroll project maintainers and
        contributors shall not be liable for any direct, indirect, incidental, special, consequential, or
        exemplary damages arising from:
    </p>
    <ul>
        <li>Your access to or use of (or inability to access or use) the official demo or the Scroll software.</li>
        <li>Any errors, inaccuracies, or omissions in payroll calculations, tax figures, or other outputs of the system.</li>
        <li>Unauthorized access to or alteration of your data.</li>
        <li>Loss of data resulting from demo resets or system changes.</li>
        <li>Any conduct of third parties using the demo or operating a self-hosted instance.</li>
        <li>Any regulatory penalties, fines, or legal consequences arising from an Operator's failure to comply with applicable law.</li>
    </ul>
    <p>
        This limitation applies regardless of the theory of liability (contract, tort, negligence, strict
        liability, or otherwise) and even if the Scroll project maintainers have been advised of the
        possibility of such damages.
    </p>
    <p>
        Nothing in these Terms excludes or limits liability for death or personal injury caused by negligence,
        fraud, or any other liability that cannot be excluded under applicable Indonesian law.
    </p>

    <!-- Section 10 -->
    <h2 id="indemnification">10. Indemnification</h2>
    <p>
        By using the official Scroll demo or operating a self-hosted Scroll instance, you agree to
        defend, indemnify, and hold harmless the Scroll project maintainers and contributors from and
        against any claims, damages, losses, liabilities, costs, and expenses (including reasonable legal fees)
        arising from:
    </p>
    <ul>
        <li>Your violation of these Terms.</li>
        <li>Your violation of any applicable law or regulation, including UU ITE or UU PDP.</li>
        <li>Your use of Scroll in a manner that causes harm to a third party.</li>
        <li>Any data processed through a self-hosted instance you operate.</li>
        <li>Any claims by your employees, contractors, or regulators arising from your use of the software.</li>
    </ul>

    <!-- Section 11 -->
    <h2 id="termination">11. Termination &amp; Access Revocation</h2>
    <p>
        The Scroll project maintainers reserve the right to suspend or revoke your access to the official
        demo at any time, without notice, for any reason, including but not limited to:
    </p>
    <ul>
        <li>Violation of these Terms or applicable law.</li>
        <li>Suspected security threat, abuse, or misuse of the demo environment.</li>
        <li>Decommissioning or modification of the demo environment.</li>
    </ul>
    <p>
        Upon termination, your right to access the demo ceases immediately. Data you entered into the demo
        may be deleted without further notice. Termination does not affect any rights or obligations that
        arose prior to termination.
    </p>
    <p>
        Your rights under the MIT License to use, modify, and distribute the Scroll source code are
        unaffected by any revocation of demo access.
    </p>

    <!-- Section 12 -->
    <h2 id="privacy">12. Privacy</h2>
    <p>
        Your use of the official Scroll demo is also governed by the
        <a href="{{ route('privacy-policy') }}">Privacy Policy</a>, which describes how personal data is
        collected, used, and protected in connection with the demo. The Privacy Policy is incorporated into
        these Terms by reference.
    </p>
    <p>
        For self-hosted Scroll installations, the Operator is the data controller under UU PDP and is
        solely responsible for their own privacy practices and compliance.
    </p>

    <!-- Section 13 -->
    <h2 id="changes">13. Changes to These Terms</h2>
    <p>
        The Scroll project maintainers may update these Terms from time to time to reflect changes in the
        demo environment, the software, applicable law, or project practices. When changes are made, the
        "Last Updated" date at the top of this page will be updated.
    </p>
    <p>
        Your continued use of the official demo after updated Terms are published constitutes your acceptance
        of the updated Terms. If you do not agree to any updated Terms, you should stop using the demo.
    </p>
    <p>
        For self-hosted Operators, changes to these Terms do not impose any new obligations on your
        independent installation - your obligations are governed by the MIT License and applicable law.
    </p>

    <!-- Section 14 -->
    <h2 id="contact">14. Contact</h2>
    <p>
        For questions or concerns about these Terms, or to report a violation, please contact the
        Scroll project maintainers through the official project channels:
    </p>
    <div class="contact-card">
        <p>
            <i class="fab fa-github me-2" style="color:#64748b;"></i>
            <strong>GitHub Repository:</strong>
            <a href="https://github.com/Sultonisky/Scroll" target="_blank" rel="noopener noreferrer">
                github.com/Sultonisky/Scroll
            </a>
        </p>
        <p>
            <i class="fas fa-comment-alt me-2" style="color:#64748b;"></i>
            <strong>General Inquiries &amp; Bug Reports:</strong>
            Open an issue on the
            <a href="https://github.com/Sultonisky/Scroll/issues" target="_blank" rel="noopener noreferrer">GitHub Issues</a>
            tracker.
        </p>
        <p>
            <i class="fas fa-shield-alt me-2" style="color:#64748b;"></i>
            <strong>Security Vulnerabilities:</strong>
            Please report security issues <strong>privately</strong> via the contact information in the
            repository - do not disclose vulnerabilities in public GitHub issues.
        </p>
    </div>

    <!-- Section 15 -->
    <h2 id="applicable-law">15. Applicable Law &amp; Dispute Resolution</h2>
    <p>
        These Terms are governed by and construed in accordance with the laws of the
        <strong>Republic of Indonesia</strong>, including:
    </p>
    <ul>
        <li><strong>Undang-Undang No. 11 Tahun 2008 jo. No. 19 Tahun 2016 tentang Informasi dan Transaksi Elektronik (UU ITE)</strong> - governing electronic systems, electronic transactions, and online conduct.</li>
        <li><strong>Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong> - governing the processing of personal data.</li>
        <li><strong>Kitab Undang-Undang Hukum Perdata (KUHPerdata)</strong> - the Indonesian Civil Code, governing contractual obligations and liability.</li>
        <li>Other applicable laws and government regulations as issued from time to time.</li>
    </ul>
    <p>
        In the event of any dispute arising from or in connection with these Terms or the use of the official
        Scroll demo, the parties shall first attempt to resolve the dispute through good-faith discussion.
    </p>
    <p>
        If a dispute cannot be resolved through discussion, it shall be subject to the jurisdiction of the
        competent courts in the <strong>Republic of Indonesia</strong>.
    </p>
    <p>
        If any provision of these Terms is found to be invalid, illegal, or unenforceable under applicable
        law, the remaining provisions shall continue in full force and effect.
    </p>

    <!-- Footer -->
    <div class="footer-legal">
        <p>
            &copy; {{ date('Y') }} Scroll &bull; Open Source Self-Hosted Payroll System &bull;
            Built by <a href="https://github.com/Sultonisky" target="_blank" rel="noopener noreferrer">Mohammad Sultoni</a>
        </p>
        <p>
            <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
            &bull;
            <a href="{{ route('terms-of-service') }}">Terms of Service</a>
            &bull;
            <a href="{{ route('login') }}">Back to Login</a>
        </p>
    </div>

</div>
</body>
</html>
