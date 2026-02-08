<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>All Academies: Email Verification</title>
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

        .message strong {
            font-weight: 700;
        }

        /* CTA Button */
        .cta-button {
            display: inline-block;
            background-color: #0284c7;
            color: #ffffff;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            margin: 24px 0;
            text-align: center;
            border: 2px solid #0284c7;
        }

        .cta-button:hover {
            background-color: #0369a1;
            border-color: #0369a1;
        }

        .button-container {
            text-align: center;
            margin: 24px 0;
        }

        /* Info sections */
        .info-section {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 24px;
            margin: 24px 0;
        }

        .section-title {
            font-size: 15px;
            color: #1f2937;
            margin: 0 0 12px 0;
            font-weight: 700;
        }

        .info-text {
            font-size: 14px;
            color: #374151;
            margin: 0 0 8px 0;
            line-height: 1.5;
        }

        .info-text:last-child {
            margin-bottom: 0;
        }

        .code-link {
            word-break: break-all;
            color: #0284c7;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin-top: 8px;
            padding: 8px;
            background-color: #ffffff;
            border-radius: 4px;
            display: block;
        }

        /* Alert box */
        .alert {
            background-color: #fef3c7;
            border-left: 4px solid #d97706;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 0 6px 6px 0;
            font-size: 14px;
            color: #78350f;
            line-height: 1.5;
        }

        .alert strong {
            font-weight: 700;
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

            .cta-button {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
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

            .cta-button {
                background-color: #0284c7;
                border-color: #0284c7;
            }

            .cta-button:hover {
                background-color: #0369a1;
                border-color: #0369a1;
            }

            .info-section {
                background-color: #111827;
                border-color: #374151;
            }

            .section-title {
                color: #f3f4f6;
            }

            .info-text {
                color: #d1d5db;
            }

            .code-link {
                background-color: #374151;
                color: #93c5fd;
            }

            .alert {
                background-color: #78350f;
                border-left-color: #d97706;
                color: #fef3c7;
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
                        <p class="header-subtitle">Email Address Verification</p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td class="content">
                        <h2 class="greeting">Verify Your Email Address</h2>

                        <p class="message">
                            Welcome to All Academies! Your account has been created successfully. To complete your registration and gain full access to our platform, please verify your email address by clicking the button below.
                        </p>

                        <!-- CTA Button -->
                        <div class="button-container">
                            <a href="{{ $actionUrl }}" class="cta-button">Verify Email Address</a>
                        </div>

                        <p class="message">
                            This verification link will expire in <strong>60 minutes</strong>. Please complete this step as soon as possible to activate your account.
                        </p>

                        <!-- Alternative Link Section -->
                        <div class="info-section">
                            <p class="section-title">If the button doesn't work, copy and paste this link:</p>
                            <a href="{{ $actionUrl }}" class="code-link">{{ $actionUrl }}</a>
                        </div>

                        <!-- What's Next Section -->
                        <div class="info-section">
                            <p class="section-title">What happens next?</p>
                            <p class="info-text">✓ Click the button or link above to verify your email</p>
                            <p class="info-text">✓ You'll be redirected to sign in to your account</p>
                            <p class="info-text">✓ Start exploring All Academies and access all features</p>
                        </div>

                        <!-- Expiration Alert -->
                        <div class="alert">
                            <strong>Important:</strong> This verification link expires in 60 minutes. If it expires, you can request a new one from the verification page.
                        </div>

                        <hr class="divider">

                        <!-- Support Section -->
                        <div class="support-section">
                            <p class="support-text">
                                Need help? Contact our <a href="mailto:support@allacademies.com" class="support-link">support team</a> if you have any questions.
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
