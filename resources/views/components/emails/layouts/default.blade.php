<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        /* Client-specific Styles */
        #outlook a { padding: 0; }
        body { margin: 0; padding: 0; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        .ExternalClass { width: 100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }

        table, td { mso-table-lspace: 0pt !important; mso-table-rspace: 0pt !important; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { border-collapse: collapse; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        :root {
            color-scheme: light;
            supported-color-schemes: light;
        }

        /* Base styles - Force light mode by default */
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            background-color: #ffffff !important;
            color: #1a1a1a !important;
        }

        /* Wrapper */
        .wrapper {
            background-color: #f8fafc !important;
            padding: 40px 20px !important;
        }

        .container {
            max-width: 600px !important;
            margin: 0 auto !important;
            background-color: #ffffff !important;
        }

        /* Header with gradient background */
        .header {
            text-align: center !important;
            padding: 40px 20px 32px !important;
            background: linear-gradient(to right, #f8fafc 0%, #f1f5f9 100%) !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .logo {
            display: inline-block !important;
            margin-bottom: 20px !important;
        }

        .logo img {
            height: 40px !important;
            width: auto !important;
        }

        .header-title {
            font-size: 28px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            margin: 0 0 8px !important;
            line-height: 1.3 !important;
        }

        .header-subtitle {
            font-size: 15px !important;
            color: #64748b !important;
            margin: 0 !important;
        }

        /* Content */
        .content {
            padding: 48px 40px !important;
            background-color: #ffffff !important;
        }

        .greeting {
            font-size: 24px !important;
            font-weight: 600 !important;
            color: #1a1a1a !important;
            margin: 0 0 24px !important;
            line-height: 1.3 !important;
        }

        .text {
            font-size: 16px !important;
            line-height: 1.6 !important;
            color: #4b5563 !important;
            margin: 0 0 16px !important;
        }

        .text strong {
            color: #1a1a1a !important;
            font-weight: 600 !important;
        }

        /* Card */
        .card {
            background-color: #f8fafc !important;
            border-radius: 12px !important;
            padding: 24px !important;
            margin: 24px 0 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .card-title {
            font-size: 18px !important;
            font-weight: 600 !important;
            color: #1a1a1a !important;
            margin: 0 0 16px !important;
        }

        .card-text {
            font-size: 14px !important;
            color: #64748b !important;
            line-height: 1.6 !important;
            margin: 0 0 16px !important;
        }

        .card-meta {
            font-size: 14px !important;
            color: #64748b !important;
            line-height: 1.8 !important;
            margin: 8px 0 0 !important;
        }

        .badge {
            display: inline-block !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            margin-top: 12px !important;
        }

        .badge-primary {
            background-color: #dbeafe !important;
            color: #1e40af !important;
        }

        .badge-success {
            background-color: #dcfce7 !important;
            color: #166534 !important;
        }

        /* Button */
        .button-wrapper {
            text-align: center !important;
            margin: 32px 0 !important;
        }

        .button {
            display: inline-block !important;
            padding: 14px 32px !important;
            background-color: #2563eb !important;
            color: #ffffff !important;
            text-decoration: none !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            font-weight: 500 !important;
        }

        /* Info Box */
        .info-box {
            border-left: 3px solid #3b82f6 !important;
            background-color: #eff6ff !important;
            padding: 16px 20px !important;
            margin: 24px 0 !important;
            border-radius: 0 8px 8px 0 !important;
        }

        .info-box.warning {
            border-left-color: #f59e0b !important;
            background-color: #fffbeb !important;
        }

        .info-box.success {
            border-left-color: #10b981 !important;
            background-color: #f0fdf4 !important;
        }

        .info-box-text {
            font-size: 14px !important;
            line-height: 1.6 !important;
            color: #374151 !important;
            margin: 0 !important;
        }

        /* Footer */
        .footer {
            padding: 40px 40px 48px !important;
            text-align: center !important;
            background-color: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        .footer-text {
            font-size: 14px !important;
            line-height: 1.6 !important;
            color: #64748b !important;
            margin: 0 0 16px !important;
        }

        .footer-links {
            margin: 20px 0 !important;
        }

        .footer-link {
            color: #2563eb !important;
            font-size: 14px !important;
            margin: 0 12px !important;
            text-decoration: none !important;
        }

        .footer-divider {
            height: 1px !important;
            background-color: #e2e8f0 !important;
            border: 0 !important;
            margin: 24px 0 !important;
        }

        .footer-legal {
            font-size: 12px !important;
            color: #94a3b8 !important;
            line-height: 1.5 !important;
            margin: 8px 0 0 !important;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .dark-mode-support body {
                background-color: #0f172a !important;
                color: #f1f5f9 !important;
            }

            .dark-mode-support .wrapper {
                background-color: #0f172a !important;
            }

            .dark-mode-support .container {
                background-color: #1e293b !important;
            }

            .dark-mode-support .header {
                background: linear-gradient(to right, #020617 0%, #0f172a 100%) !important;
                border-bottom-color: #334155 !important;
            }

            .dark-mode-support .content {
                background-color: #1e293b !important;
            }

            .dark-mode-support .greeting,
            .dark-mode-support .header-title {
                color: #f1f5f9 !important;
            }

            .dark-mode-support .text {
                color: #cbd5e1 !important;
            }

            .dark-mode-support .text strong {
                color: #f1f5f9 !important;
            }

            .dark-mode-support .card {
                background-color: #0f172a !important;
                border-color: #334155 !important;
            }

            .dark-mode-support .card-title {
                color: #f1f5f9 !important;
            }

            .dark-mode-support .card-text,
            .dark-mode-support .card-meta {
                color: #94a3b8 !important;
            }

            .dark-mode-support .info-box {
                background-color: rgba(59, 130, 246, 0.1) !important;
            }

            .dark-mode-support .info-box.warning {
                background-color: rgba(245, 158, 11, 0.1) !important;
            }

            .dark-mode-support .info-box.success {
                background-color: rgba(16, 185, 129, 0.1) !important;
            }

            .dark-mode-support .info-box-text {
                color: #cbd5e1 !important;
            }

            .dark-mode-support .footer {
                background-color: #0f172a !important;
                border-top-color: #334155 !important;
            }

            .dark-mode-support .footer-text,
            .dark-mode-support .header-subtitle {
                color: #94a3b8 !important;
            }

            .dark-mode-support .footer-divider {
                background-color: #334155 !important;
            }

            .dark-mode-support .footer-legal {
                color: #64748b !important;
            }
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 20px 10px !important;
            }

            .header {
                padding: 32px 20px 24px !important;
            }

            .content {
                padding: 32px 24px !important;
            }

            .greeting {
                font-size: 20px !important;
            }

            .footer {
                padding: 32px 24px !important;
            }

            .button {
                display: block !important;
                width: 100% !important;
            }

            .card {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #ffffff; color: #1a1a1a;">

<!-- Preview Text -->
@isset($previewText)
    <div style="display: none; max-height: 0px; overflow: hidden; mso-hide: all;">
        {{ $previewText }}
    </div>
    <div style="display: none; max-height: 0px; overflow: hidden; mso-hide: all;">
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
@endisset

<div class="wrapper" style="background-color: #f8fafc; padding: 40px 20px;">
    <table role="presentation" class="container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; width: 100%; border-collapse: collapse;">

        <!-- Header with subtle gradient -->
        <tr>
            <td class="header" style="text-align: center; padding: 40px 20px 32px; background: linear-gradient(to right, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0;">
                @if($showLogo ?? true)
                    <div class="logo" style="display: inline-block; margin-bottom: 20px;">
                        <img src="{{ $logo ?? asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height: 40px; width: auto; display: block;">
                    </div>
                @endif

                @isset($headerTitle)
                    <h1 class="header-title" style="font-size: 28px; font-weight: 600; color: #0f172a; margin: 0 0 8px; line-height: 1.3;">
                        {{ $headerTitle }}
                    </h1>
                @endisset

                @isset($headerSubtitle)
                    <p class="header-subtitle" style="font-size: 15px; color: #64748b; margin: 0;">
                        {{ $headerSubtitle }}
                    </p>
                @endisset
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td class="content" style="padding: 48px 40px; background-color: #ffffff;">
                {{ $slot }}
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td class="footer" style="padding: 40px 40px 48px; text-align: center; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                <p class="footer-text" style="font-size: 14px; line-height: 1.6; color: #64748b; margin: 0 0 16px;">
                    You're receiving this email as a member of {{ config('app.name') }}
                </p>

                <div class="footer-links" style="margin: 20px 0;">
                    <a href="{{ config('app.url') }}" class="footer-link" style="color: #2563eb; font-size: 14px; margin: 0 12px; text-decoration: none;">Home</a>
                    <a href="{{ config('app.url') }}/help" class="footer-link" style="color: #2563eb; font-size: 14px; margin: 0 12px; text-decoration: none;">Help</a>
                    <a href="{{ config('app.url') }}/contact" class="footer-link" style="color: #2563eb; font-size: 14px; margin: 0 12px; text-decoration: none;">Contact</a>
                </div>

                <hr class="footer-divider" style="height: 1px; background-color: #e2e8f0; border: 0; margin: 24px 0;">

                <p class="footer-legal" style="font-size: 12px; color: #94a3b8; line-height: 1.5; margin: 8px 0 0;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    @isset($companyAddress)
                        <br>{{ $companyAddress }}
                    @endisset
                </p>

                @if($showUnsubscribe ?? false)
                    <p class="footer-legal" style="font-size: 12px; color: #94a3b8; margin: 12px 0 0;">
                        <a href="{{ $unsubscribeUrl ?? '#' }}" style="color: #94a3b8; text-decoration: underline;">Unsubscribe</a>
                    </p>
                @endif
            </td>
        </tr>

    </table>
</div>

</body>
</html>
