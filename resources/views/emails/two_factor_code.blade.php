<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>All Academies: Account Verification Code</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        /* Base styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f9fafb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.5;
            color: #1f2937;
        }

        /* Container */
        .email-wrapper {
            background-color: #f9fafb;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        /* Header */
        .header {
            background-color: #1f2937;
            padding: 40px 32px;
            text-align: center;
        }

        .header-title {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.25px;
        }

        .header-subtitle {
            color: #d1d5db;
            font-size: 14px;
            margin: 8px 0 0 0;
            font-weight: 400;
        }

        /* Content */
        .content {
            padding: 40px 32px;
            background-color: #ffffff;
        }

        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin: 0 0 16px 0;
            font-weight: 700;
        }

        .message {
            font-size: 15px;
            color: #374151;
            margin: 0 0 32px 0;
            line-height: 1.6;
        }

        /* Verification code box */
        .code-section {
            margin: 32px 0;
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 32px 24px;
            text-align: center;
        }

        .code-label {
            font-size: 12px;
            color: #6b7280;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
        }

        .verification-code {
            font-size: 48px;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: 8px;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
            display: inline-block;
            word-spacing: 8px;
        }

        .code-hint {
            font-size: 13px;
            color: #6b7280;
            margin: 16px 0 0 0;
            font-weight: 400;
        }

        /* Alert boxes - WCAG AA contrast compliant */
        .alert {
            margin: 24px 0;
            padding: 16px 20px;
            border-radius: 6px;
            border-left: 4px solid;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-warning {
            background-color: #fef3c7;
            border-left-color: #d97706;
            color: #78350f;
        }

        .alert-info {
            background-color: #dbeafe;
            border-left-color: #0284c7;
            color: #003366;
        }

        .alert-danger {
            background-color: #fee2e2;
            border-left-color: #dc2626;
            color: #7f1d1d;
        }

        .alert-title {
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .alert-text {
            margin: 0;
        }

        .alert strong {
            font-weight: 700;
        }

        /* Instructions */
        .instructions-section {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 24px;
            margin: 24px 0;
        }

        .section-title {
            font-size: 15px;
            color: #1f2937;
            margin: 0 0 16px 0;
            font-weight: 700;
        }

        .instructions-list {
            margin: 0;
            padding-left: 24px;
        }

        .instructions-list li {
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .instructions-list li:last-child {
            margin-bottom: 0;
        }

        /* Divider */
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 32px 0;
            border: none;
        }

        /* Support section */
        .support-section {
            background-color: #f9fafb;
            border: 1px dashed #d1d5db;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin: 32px 0 0 0;
        }

        .support-text {
            font-size: 14px;
            color: #374151;
            margin: 0;
            line-height: 1.5;
        }

        .support-link {
            color: #0284c7;
            font-weight: 600;
            text-decoration: none;
        }

        .support-link:hover {
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            background-color: #f9fafb;
            padding: 32px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        .footer-text {
            margin: 0 0 16px 0;
        }

        .footer-links {
            margin: 20px 0 0 0;
            padding: 0;
            list-style: none;
            font-size: 12px;
        }

        .footer-links li {
            display: inline-block;
            margin: 0 12px;
        }

        .footer-links a {
            color: #6b7280;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px !important;
            }

            .header {
                padding: 32px 20px !important;
            }

            .content {
                padding: 24px 20px !important;
            }

            .verification-code {
                font-size: 36px !important;
                letter-spacing: 6px !important;
            }

            .code-section {
                padding: 24px 16px !important;
            }

            .footer {
                padding: 24px 20px !important;
            }

            .footer-links li {
                display: block !important;
                margin: 6px 0 !important;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827;
                color: #e5e7eb;
            }

            .email-wrapper {
                background-color: #111827;
            }

            .email-container {
                background-color: #1f2937;
                border-color: #374151;
            }

            .header {
                background-color: #111827;
            }

            .header-subtitle {
                color: #9ca3af;
            }

            .content {
                background-color: #1f2937;
            }

            .greeting {
                color: #f3f4f6;
            }

            .message {
                color: #d1d5db;
            }

            .code-section {
                background-color: #111827;
                border-color: #374151;
            }

            .verification-code {
                color: #f3f4f6;
                background-color: #374151;
                border-color: #4b5563;
            }

            .code-label {
                color: #9ca3af;
            }

            .code-hint {
                color: #9ca3af;
            }

            .instructions-section {
                background-color: #111827;
                border-color: #374151;
            }

            .section-title {
                color: #f3f4f6;
            }

            .instructions-list li {
                color: #d1d5db;
            }

            .support-section {
                background-color: #111827;
                border-color: #374151;
            }

            .support-text {
                color: #d1d5db;
            }

            .footer {
                background-color: #111827;
                border-top-color: #374151;
                color: #9ca3af;
            }

            .footer-links a {
                color: #9ca3af;
            }

            .divider {
                background-color: #374151;
            }
        }
    </style>
</head>
<body>
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="email-wrapper">
    <tr>
        <td align="center">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container">
                <!-- Header -->
                <tr>
                    <td class="header">
                        <h1 class="header-title">All Academies</h1>
                        <p class="header-subtitle">Account Security Verification</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td class="content">
                        <h2 class="greeting">Verify Your Login</h2>

                        <p class="message">
                            A login attempt was made on your All Academies account. To complete the sign-in process, please use the verification code below.
                        </p>

                        <!-- Verification Code -->
                        <div class="code-section">
                            <p class="code-label">Your Verification Code</p>
                            <p class="verification-code">{{ $code }}</p>
                            <p class="code-hint">Enter this code in the verification screen to proceed</p>
                        </div>

                        <!-- Expiration Warning -->
                        <div class="alert alert-warning">
                            <p class="alert-title">Code Expires in 1 Hour</p>
                            <p class="alert-text">This code will expire in 1 hour. Please complete your authentication before it expires.</p>
                        </div>

                        <!-- Instructions -->
                        <div class="instructions-section">
                            <h3 class="section-title">Next Steps</h3>
                            <ol class="instructions-list">
                                <li>Copy or note the 6-digit code above</li>
                                <li>Return to the All Academies login page</li>
                                <li>Enter the code in the verification field</li>
                                <li>Click "Verify" to complete sign-in</li>
                            </ol>
                        </div>

                        <!-- Security Notice -->
                        <div class="alert alert-danger">
                            <p class="alert-title">Security Reminder</p>
                            <p class="alert-text">
                                <strong>Never share this code</strong> with anyone. All Academies staff will never ask you for this code. If you didn't attempt to log in, change your password immediately and contact support.
                            </p>
                        </div>

                        <hr class="divider">

                        <!-- Support Section -->
                        <div class="support-section">
                            <p class="support-text">
                                Need help? Contact our <a href="{{ route('branding.contact') }}" class="support-link">support team</a> or visit the <a href="#" class="support-link">help center</a>.
                            </p>
                        </div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td class="footer">
                        <p class="footer-text">
                            This is an automated security email from All Academies.<br>
                            Please do not reply to this email.
                        </p>

                        <p class="footer-text" style="font-size: 12px; color: #6b7280; margin-top: 16px;">
                            &copy; {{ date('Y') }} All Academies. All rights reserved.
                        </p>

                        <ul class="footer-links">
                            <li><a href="{{ route('branding.privacy') }}">Privacy Policy</a></li>
                            <li><a href="{{ route('branding.terms') }}">Terms of Service</a></li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
