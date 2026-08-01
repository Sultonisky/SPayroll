<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Scroll Privacy Policy - how personal data is handled on the official demo environment.">
    <title>Scroll | Privacy Policy</title>
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
        .badge-uu { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 600; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 20px; padding: 3px 10px; }
        .last-updated { font-size: 13px; color: #64748b; }
        .intro-box { background: #f1f5f9; border-left: 3px solid #3b82f6; border-radius: 6px; padding: 14px 18px; margin-bottom: 36px; }
        .intro-box p { margin: 0; font-size: 14px; line-height: 1.7; color: #334155; }
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
        .highlight-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 16px; margin: 14px 0; }
        .highlight-box p { margin: 0; font-size: 14px; }
        .warn-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 12px 16px; margin: 14px 0; }
        .warn-box p { margin: 0; font-size: 14px; color: #9a3412; }
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

    <h1>Privacy Policy</h1>
    <div class="meta-row">
        <span class="last-updated">Last Updated: {{ date('F d, Y') }}</span>
        <span class="badge-uu"><i class="fas fa-shield-alt"></i> Aligned with UU PDP No. 27/2022</span>
    </div>

    <div class="intro-box">
        <p>
            Scroll is an open-source, self-hosted payroll management system designed to help organizations manage
            employee and payroll information on their own infrastructure. This Privacy Policy explains how personal
            data is handled when you access the <strong>official Scroll live demo</strong> operated by the
            Scroll project maintainers. It is intended to comply with applicable Indonesian law, including
            <strong>Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong>.
        </p>
    </div>

    <div class="warn-box">
        <p>
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Self-hosted instances:</strong> This Privacy Policy applies only to the official demo.
            Organizations operating their own Scroll installation are independently responsible for their own
            privacy practices and for complying with UU PDP and all other applicable laws.
        </p>
    </div>

    <!-- Table of Contents -->
    <div class="toc">
        <h3>Contents</h3>
        <ol>
            <li><a href="#scope">Scope of This Privacy Policy</a></li>
            <li><a href="#data-collected">Data We Collect</a></li>
            <li><a href="#demo-data">Demo Data &amp; Prohibited Information</a></li>
            <li><a href="#how-we-use">How We Use Personal Data</a></li>
            <li><a href="#legal-basis">Legal Basis for Processing (UU PDP)</a></li>
            <li><a href="#retention">Data Retention</a></li>
            <li><a href="#sharing">Data Sharing &amp; Third-Party Services</a></li>
            <li><a href="#security">Data Security</a></li>
            <li><a href="#incident">Security Incident Response</a></li>
            <li><a href="#rights">Your Privacy Rights</a></li>
            <li><a href="#self-hosted">Self-Hosted Scroll Instances</a></li>
            <li><a href="#open-source">Open Source Software</a></li>
            <li><a href="#external-links">External Links</a></li>
            <li><a href="#children">Children's Privacy</a></li>
            <li><a href="#changes">Changes to This Privacy Policy</a></li>
            <li><a href="#contact">Contact Us</a></li>
            <li><a href="#applicable-law">Applicable Law</a></li>
        </ol>
    </div>

    <!-- Section 1 -->
    <h2 id="scope">1. Scope of This Privacy Policy</h2>
    <p>
        This Privacy Policy applies only to the <strong>official Scroll demo environment</strong> operated by the
        Scroll project maintainers. The demo is provided for demonstration, evaluation, and educational purposes,
        and may contain sample or dummy employee, payroll, and company data to showcase application functionality.
    </p>
    <p>This Privacy Policy does <strong>not</strong> apply to:</p>
    <ul>
        <li>Independently deployed, self-hosted Scroll instances.</li>
        <li>Third-party websites or services linked from the Scroll project.</li>
        <li>Services not operated or controlled by the Scroll project maintainers.</li>
    </ul>
    <p>
        By accessing the official Scroll demo, you acknowledge and agree to the practices described in this
        Privacy Policy.
    </p>

    <!-- Section 2 -->
    <h2 id="data-collected">2. Data We Collect</h2>
    <p>
        In operating the official demo, we may collect or process limited categories of personal data as described
        below. We apply the principle of <strong>data minimization</strong> in accordance with UU PDP - collecting
        only what is reasonably necessary for the stated purposes.
    </p>

    <h3>2.1 Account and Authentication Information</h3>
    <p>When you access the demo, we may process:</p>
    <ul>
        <li>Name, where applicable.</li>
        <li>Email address or username.</li>
        <li>Authentication credentials (passwords are stored using secure hashing - never in plain text).</li>
        <li>User role or access permissions assigned within the system.</li>
    </ul>
    <p>
        Where the demo provides shared or predefined credentials, those credentials are intended solely for
        demonstration purposes and carry no expectation of personal confidentiality.
    </p>

    <h3>2.2 Application Activity</h3>
    <p>
        The demo may record activities performed within the application for operational, security, and auditing
        purposes, including:
    </p>
    <ul>
        <li>Records created, updated, or deleted (e.g., employee entries, payroll data, bonus records).</li>
        <li>User actions and activities within the payroll and attendance management modules.</li>
        <li>Authentication and login events.</li>
        <li>Timestamps associated with activities.</li>
        <li>User roles or permissions associated with actions.</li>
    </ul>

    <h3>2.3 Technical and Security Information</h3>
    <p>
        For security, debugging, and system maintenance, the demo environment may process limited technical
        information:
    </p>
    <ul>
        <li>IP address.</li>
        <li>Browser or user-agent string.</li>
        <li>Device or operating system information where available.</li>
        <li>Request timestamps.</li>
        <li>Error and application logs.</li>
        <li>Authentication and security events.</li>
    </ul>
    <p>We collect only technical information that is reasonably necessary to operate, secure, troubleshoot, and maintain the demo.</p>

    <h3>2.4 Information You Choose to Submit</h3>
    <p>
        If you voluntarily contact the Scroll project maintainers through available communication channels,
        we may process the information you provide in order to respond to your inquiry.
    </p>
    <div class="warn-box">
        <p>
            <i class="fas fa-exclamation-triangle me-1"></i>
            Do not submit confidential employee data, payroll records, financial information, or other sensitive
            personal data through public channels such as GitHub issues.
        </p>
    </div>

    <!-- Section 3 -->
    <h2 id="demo-data">3. Demo Data &amp; Prohibited Information</h2>
    <p>
        The official Scroll demo is strictly intended for demonstration and evaluation. Because the demo is a
        <strong>publicly accessible environment</strong>:
    </p>
    <div class="warn-box">
        <p>
            <i class="fas fa-ban me-1"></i>
            <strong>Do not enter or upload</strong> real employee names, NIK (national identity numbers), payroll
            figures, bank account information, NPWP (tax identification numbers), salary details, or any other
            confidential or sensitive personal data into the demo environment.
        </p>
    </div>
    <ul>
        <li>The demo environment may be reset, modified, or replaced periodically.</li>
        <li>Any data entered into the demo may be deleted as part of routine maintenance or system updates.</li>
        <li>Use only fictional or clearly non-sensitive information when testing the demo.</li>
    </ul>

    <!-- Section 4 -->
    <h2 id="how-we-use">4. How We Use Personal Data</h2>
    <p>Personal data processed through the official demo may be used to:</p>
    <ul>
        <li>Provide access to and operate the Scroll demo environment.</li>
        <li>Authenticate users and manage access permissions.</li>
        <li>Maintain application functionality across all modules (employees, payroll, bonuses, attendance).</li>
        <li>Monitor system stability and performance.</li>
        <li>Detect, prevent, and investigate unauthorized access or misuse.</li>
        <li>Identify and resolve technical problems or security vulnerabilities.</li>
        <li>Maintain security and protect the integrity of the system.</li>
        <li>Respond to questions, reports, or requests submitted to the project maintainers.</li>
        <li>Comply with applicable legal obligations where required.</li>
    </ul>
    <p>
        We <strong>do not sell personal data</strong>. We do not use personal data collected through the demo
        for targeted advertising or unrelated marketing purposes.
    </p>

    <!-- Section 5 -->
    <h2 id="legal-basis">5. Legal Basis for Processing (UU PDP)</h2>
    <p>
        Under <strong>Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong>,
        personal data may only be processed on a lawful basis. For the limited data processed through the
        official demo, the primary bases are:
    </p>
    <ul>
        <li><strong>Consent (Persetujuan)</strong> - by voluntarily accessing and using the demo, you consent to the limited processing described in this policy.</li>
        <li><strong>Legitimate interests (Kepentingan yang sah)</strong> - security monitoring, system integrity, and fraud prevention necessary to operate a publicly accessible demo environment.</li>
        <li><strong>Legal obligation (Kewajiban hukum)</strong> - where processing is required to comply with applicable Indonesian law.</li>
    </ul>
    <p>
        Where consent is the applicable basis, you may withdraw it at any time by ceasing to use the demo.
        Withdrawal does not affect the lawfulness of processing already carried out.
    </p>
    <div class="highlight-box">
        <p>
            <i class="fas fa-info-circle me-1" style="color: #1d4ed8;"></i>
            <strong>Note for self-hosted operators:</strong> Organizations deploying Scroll to process employee
            payroll data (salary, BPJS contributions, PPh 21, bank account information) act as an independent
            <em>Pengendali Data Pribadi</em> (Data Controller) under UU PDP. They must establish their own lawful
            basis - typically employment contract necessity or legal obligation - and issue appropriate privacy
            notices to their employees.
        </p>
    </div>

    <!-- Section 6 -->
    <h2 id="retention">6. Data Retention</h2>
    <p>
        We retain personal data only for as long as reasonably necessary to fulfill the purposes described in
        this Privacy Policy, or as required by applicable law. In line with UU PDP's principle of storage
        limitation:
    </p>
    <ul>
        <li><strong>Demo application data</strong> may be reset or deleted periodically as part of routine maintenance.</li>
        <li><strong>Account information</strong> may be retained while necessary to provide access to the demo.</li>
        <li><strong>Security and technical logs</strong> may be retained for a reasonable period for security, troubleshooting, and operational purposes.</li>
        <li>Data is deleted when it is no longer necessary for the purposes for which it was collected, subject to any applicable legal retention requirements.</li>
    </ul>
    <p>Retention periods may vary depending on the type and purpose of the information.</p>

    <!-- Section 7 -->
    <h2 id="sharing">7. Data Sharing &amp; Third-Party Services</h2>
    <p>We <strong>do not sell personal data</strong>.</p>
    <p>
        We may use third-party infrastructure or service providers when necessary to host, operate, secure,
        deliver, or maintain the official Scroll demo. Such providers may process limited technical or
        operational information as necessary to provide their services, and are expected to do so in accordance
        with applicable contractual or legal requirements.
    </p>
    <p>We may also disclose information where:</p>
    <ul>
        <li>Required by applicable Indonesian law or regulation.</li>
        <li>Required by a valid legal process or lawful request from a competent authority (<em>penegak hukum</em>).</li>
        <li>Necessary to protect the rights, property, or safety of the Scroll project, its users, or the public.</li>
    </ul>
    <p>
        The Scroll project maintainers do not intentionally disclose personal data to third parties for their
        own marketing purposes.
    </p>

    <!-- Section 8 -->
    <h2 id="security">8. Data Security</h2>
    <p>
        We take reasonable technical and organizational measures to protect personal data processed through the
        official demo against unauthorized access, loss, misuse, alteration, disclosure, or destruction.
        Security measures implemented in Scroll include, where appropriate:
    </p>
    <ul>
        <li>Role-based access controls (Admin, HR, Finance roles with enforced permissions).</li>
        <li>Authentication mechanisms with session management.</li>
        <li>Secure password hashing (passwords are never stored in plain text).</li>
        <li>CSRF protection on all state-changing requests.</li>
        <li>Application-level security headers.</li>
        <li>Logging and monitoring of security-related events.</li>
        <li>Regular maintenance and dependency updates.</li>
    </ul>
    <p>
        No method of electronic transmission or storage can be guaranteed to be completely secure. While we take
        reasonable measures to protect information, we cannot guarantee absolute security. Users are responsible
        for maintaining the confidentiality of any credentials provided to them and for using the demo only for
        its intended, legitimate purpose.
    </p>

    <!-- Section 9 -->
    <h2 id="incident">9. Security Incident Response</h2>
    <p>
        If we become aware of a personal data security incident affecting the official Scroll demo, we will
        take reasonable steps to investigate, contain, and address the incident.
    </p>
    <p>
        Where a breach is likely to create a risk to the rights and freedoms of individuals, we will make
        reasonable efforts to notify affected users through the available contact channels (e.g., GitHub
        repository announcements) as promptly as practicable. We will also comply with any notification
        obligations required by applicable Indonesian law.
    </p>
    <div class="highlight-box">
        <p>
            <i class="fas fa-info-circle me-1" style="color: #1d4ed8;"></i>
            <strong>Note for self-hosted operators:</strong> Under UU PDP, organizations operating their own
            Scroll instance must notify the relevant supervisory authority and affected data subjects
            <strong>within 3 × 24 hours (72 hours)</strong> of becoming aware of a breach that poses risk to
            data subjects' rights. This obligation rests with the organization operating the instance, not
            the Scroll project maintainers.
        </p>
    </div>
    <p>Incident response measures we may take include:</p>
    <ul>
        <li>Investigating the cause and scope of the incident.</li>
        <li>Taking steps to contain and mitigate the impact.</li>
        <li>Restoring affected systems.</li>
        <li>Implementing corrective security measures.</li>
        <li>Providing public disclosure through official project channels where appropriate.</li>
    </ul>

    <!-- Section 10 -->
    <h2 id="rights">10. Your Privacy Rights</h2>
    <p>
        Under <strong>UU PDP No. 27 Tahun 2022</strong>, data subjects have the following rights with respect
        to their personal data:
    </p>
    <ul>
        <li><strong>Hak Akses (Right of Access)</strong> - the right to request information about personal data processed about you and to receive a copy of that data.</li>
        <li><strong>Hak Koreksi (Right to Rectification)</strong> - the right to request correction of inaccurate, incomplete, or outdated personal data.</li>
        <li><strong>Hak Penghapusan (Right to Erasure)</strong> - the right to request deletion of personal data where permitted by applicable law.</li>
        <li><strong>Hak Pembatasan (Right to Restriction)</strong> - the right to request that processing be restricted in certain circumstances.</li>
        <li><strong>Hak Portabilitas (Right to Data Portability)</strong> - the right to receive personal data in a structured, commonly used, machine-readable format, in accordance with Article 13 UU PDP.</li>
        <li><strong>Hak Menarik Persetujuan (Right to Withdraw Consent)</strong> - where processing is based on consent, the right to withdraw consent at any time without affecting the lawfulness of prior processing.</li>
        <li><strong>Hak Keberatan (Right to Object)</strong> - the right to object to certain types of processing in circumstances permitted by law.</li>
        <li><strong>Hak Informasi (Right to Information)</strong> - the right to receive clear information about how personal data is processed.</li>
    </ul>
    <p>
        Requests relating to personal data processed through the official Scroll demo may be submitted using
        the contact information in the <a href="#contact">Contact Us</a> section below. We may need to verify
        the identity of the requester before fulfilling certain requests. Some rights may be subject to
        limitations or exceptions under applicable law.
    </p>

    <!-- Section 11 -->
    <h2 id="self-hosted">11. Self-Hosted Scroll Instances</h2>
    <p>
        Scroll is designed as open-source, self-hosted software. Organizations may download, install, modify,
        and operate Scroll on infrastructure under their own control, subject to the applicable open-source
        license.
    </p>
    <p>
        When an organization operates its own Scroll instance, that organization acts as the
        <strong>Pengendali Data Pribadi (Data Controller)</strong> under UU PDP. As such, the organization is
        solely responsible for:
    </p>
    <ul>
        <li>Determining the purposes for which employee and payroll data (including salary, PPh 21, BPJS, bank account data) is processed.</li>
        <li>Establishing an appropriate lawful basis for all processing activities.</li>
        <li>Securing its infrastructure, database, and application configuration.</li>
        <li>Establishing and publishing appropriate privacy notices or policies for its employees and users.</li>
        <li>Complying with UU PDP and all other applicable data protection, employment, taxation, and regulatory requirements in Indonesia.</li>
        <li>Appointing a Data Protection Officer (<em>Petugas Pelindungan Data Pribadi</em>) if required under applicable regulations.</li>
        <li>Responding to data subject rights requests from their own employees or users.</li>
    </ul>
    <div class="highlight-box">
        <p>
            <i class="fas fa-info-circle me-1" style="color: #1d4ed8;"></i>
            The Scroll project maintainers do not have access to data stored in independently operated
            self-hosted instances, and are not responsible for the privacy practices, security configuration,
            or data processing activities of such instances.
        </p>
    </div>
    <p>
        Organizations deploying Scroll should conduct their own legal, privacy, and security assessments
        and implement safeguards appropriate to their specific operational context.
    </p>

    <!-- Section 12 -->
    <h2 id="open-source">12. Open Source Software</h2>
    <p>
        Scroll is distributed as open-source software under the applicable license specified in the
        <a href="https://github.com/Sultonisky/SPayroll" target="_blank" rel="noopener noreferrer">project repository</a>.
        The open-source nature of the software does not transfer privacy or data protection responsibilities from an
        organization operating a self-hosted instance to the Scroll project maintainers. Each organization is
        responsible for how the software is configured and used within its own environment.
    </p>

    <!-- Section 13 -->
    <h2 id="external-links">13. External Links</h2>
    <p>
        The Scroll project may provide links to external websites, repositories, documentation, or third-party
        services. These external services are not controlled by the Scroll project maintainers, and we are not
        responsible for their privacy practices, content, or security. We encourage users to review the privacy
        policies of any third-party services they access.
    </p>

    <!-- Section 14 -->
    <h2 id="children">14. Children's Privacy</h2>
    <p>
        The Scroll demo is intended exclusively for organizational and professional use by adults. The demo is
        not directed toward children, and we do not knowingly seek to collect personal data from individuals under
        the age of 18. If you believe that personal data belonging to a child has been submitted to the demo,
        please contact us so that we can take appropriate action in accordance with applicable law.
    </p>

    <!-- Section 15 -->
    <h2 id="changes">15. Changes to This Privacy Policy</h2>
    <p>
        We may update this Privacy Policy from time to time to reflect changes in the Scroll project, the
        official demo environment, applicable laws (including UU PDP implementing regulations), or our data
        handling practices. When changes are made, the "Last Updated" date at the top of this page will be
        updated.
    </p>
    <p>
        Where required by applicable law, we may provide additional notice or obtain consent for material changes.
        We encourage users to periodically review this Privacy Policy to stay informed about how personal data
        is handled.
    </p>

    <!-- Section 16 -->
    <h2 id="contact">16. Contact Us</h2>
    <p>
        If you have questions, concerns, or requests regarding this Privacy Policy or the processing of personal
        data through the official Scroll demo, please contact the Scroll project maintainers through the
        official project contact channels below.
    </p>
    <div class="contact-card">
        <p><i class="fab fa-github me-2" style="color:#64748b;"></i><strong>GitHub Repository:</strong>
            <a href="https://github.com/Sultonisky/SPayroll" target="_blank" rel="noopener noreferrer">
                github.com/Sultonisky/SPayroll
            </a>
        </p>
        <p><i class="fas fa-comment-alt me-2" style="color:#64748b;"></i><strong>Privacy &amp; Data Inquiries:</strong>
            Open a <a href="https://github.com/Sultonisky/SPayroll/issues" target="_blank" rel="noopener noreferrer">GitHub Issue</a>
            and label it <code>privacy</code>. For sensitive matters that should not be public, reach out via
            the contact information listed in the repository.
        </p>
    </div>
    <div class="warn-box" style="margin-top:14px;">
        <p>
            <i class="fas fa-exclamation-triangle me-1"></i>
            Please do not include sensitive personal data, employee information, payroll records, passwords,
            bank account details, or other confidential information in public GitHub issues.
        </p>
    </div>

    <!-- Section 17 -->
    <h2 id="applicable-law">17. Applicable Law</h2>
    <p>
        This Privacy Policy is intended to be interpreted in accordance with the laws and regulations of the
        <strong>Republic of Indonesia</strong>, including:
    </p>
    <ul>
        <li><strong>Undang-Undang No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong> - Indonesia's primary personal data protection framework, fully in force since October 17, 2024.</li>
        <li><strong>Undang-Undang No. 11 Tahun 2008 jo. No. 19 Tahun 2016 tentang Informasi dan Transaksi Elektronik (UU ITE)</strong> - governing electronic information and transactions.</li>
        <li>Other applicable laws and government regulations as issued from time to time.</li>
    </ul>
    <p>
        If any provision of this Privacy Policy is found to be invalid or unenforceable under applicable law,
        the remaining provisions will continue to apply to the fullest extent permitted.
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
