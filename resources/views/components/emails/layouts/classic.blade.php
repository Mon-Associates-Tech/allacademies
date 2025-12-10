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

        /* Base styles */
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: Georgia, 'Times New Roman', Times, serif !important;
            -webkit-font-smoothing: antialiased !important;
            background-color: #f4f4f5 !important;
            color: #18181b !important;
        }

        /* Wrapper */
        .wrapper {
            background-color: #f4f4f5 !important;
            padding: 30px 15px !important;
        }

        .container {
            max-width: 600px !important;
            margin: 0 auto !important;
            background-color: #ffffff !important;
            border: 1px solid #e4e4e7 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
        }

        /* Header with subtle gradient */
        .header {
            background: linear-gradient(to right, #f4f4f5 0%, #e4e4e7 100%) !important;
            padding: 32px 40px !important;
            text-align: center !important;
            border-bottom: 2px solid #d4d4d8 !important;
        }

        .logo {
            display: inline-block !important;
            margin-bottom: 16px !important;
        }

        .logo img {
            height: 45px !important;
            width: auto !important;
        }

        .header-title {
            font-size: 26px !important;
            font-weight: 700 !important;
            color: #18181b !important;
            margin: 0 0 8px !important;
            line-height: 1.2 !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }

        .header-subtitle {
            font-size: 14px !important;
            color: #52525b !important;
            margin: 0 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-weight: 400 !important;
        }

        /* Content */
        .content {
            padding: 40px 40px !important;
            background-color: #ffffff !important;
        }

        .greeting {
            font-size: 22px !important;
            font-weight: 600 !important;
            color: #18181b !important;
            margin: 0 0 20px !important;
            line-height: 1.3 !important;
            font-family: Arial, Helvetica, sans-serif !important;
        }

        .text {
            font-size: 16px !important;
            line-height: 1.7 !important;
            color: #52525b !important;
            margin: 0 0 16px !important;
        }

        .text strong {
            color: #18181b !important;
            font-weight: 700 !important;
        }

        /* Card */
        .card {
            background-color: #fafafa !important;
            border: 1px solid #e4e4e7 !important;
            padding: 24px !important;
            margin: 24px 0 !important;
        }

        .card-title {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #18181b !important;
            margin: 0 0 12px !important;
            font-family: Arial, Helvetica, sans-serif !important;
            border-bottom: 2px solid #3b82f6 !important;
            padding-bottom: 8px !important;
        }

        .card-text {
            font-size: 14px !important;
            color: #71717a !important;
            line-height: 1.6 !important;
            margin: 0 0 16px !important;
        }

        .card-meta {
            font-size: 13px !important;
            color: #71717a !important;
            line-height: 1.8 !important;
            margin: 12px 0 0 !important;
            border-top: 1px solid #e4e4e7 !important;
            padding-top: 12px !important;
        }

        .badge {
            display: inline-block !important;
            padding: 4px 12px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            margin-top: 12px !important;
            font-family: Arial, Helvetica, sans-serif !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .badge-primary {
            background-color: #dbeafe !important;
            color: #1e40af !important;
            border: 1px solid #93c5fd !important;
        }

        .badge-success {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            border: 1px solid #86efac !important;
        }

        /* Button */
        .button-wrapper {
            text-align: center !important;
            margin: 32px 0 !important;
        }

        .button {
            display: inline-block !important;
            padding: 16px 40px !important;
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            border: 2px solid #3b82f6 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        /* Info Box */
        .info-box {
            border: 2px solid #3b82f6 !important;
            background-color: #eff6ff !important;
            padding: 20px !important;
            margin: 24px 0 !important;
        }

        .info-box.warning {
            border-color: #f59e0b !important;
            background-color: #fffbeb !important;
        }

        .info-box.success {
            border-color: #10b981 !important;
            background-color: #f0fdf4 !important;
        }

        .info-box-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #18181b !important;
            margin: 0 0 8px !important;
            font-family: Arial, Helvetica, sans-serif !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .info-box-text {
            font-size: 14px !important;
            line-height: 1.6 !important;
            color: #52525b !important;
            margin: 0 !important;
        }

        /* Divider */
        .divider {
            height: 2px !important;
            background-color: #e4e4e7 !important;
            margin: 32px 0 !important;
            border: 0 !important;
        }

        /* Footer */
        .footer {
            background: linear-gradient(to right, #fafafa 0%, #f4f4f5 100%) !important;
            padding: 32px 40px !important;
            text-align: center !important;
            border-top: 2px solid #e4e4e7 !important;
        }

        .footer-text {
            font-size: 13px !important;
            line-height: 1.6 !important;
            color: #71717a !important;
            margin: 0 0 16px !important;
        }

        .footer-links {
            margin: 16px 0 !important;
        }

        .footer-link {
            color: #3b82f6 !important;
            font-size: 13px !important;
            margin: 0 10px !important;
            text-decoration: underline !important;
            font-weight: 600 !important;
        }

        .footer-divider {
            height: 1px !important;
            background-color: #e4e4e7 !important;
            border: 0 !important;
            margin: 20px 0 !important;
        }

        .footer-legal {
            font-size: 11px !important;
            color: #a1a1aa !important;
            line-height: 1.5 !important;
            margin: 8px 0 0 !important;
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .dark-mode-support body {
                background-color: #09090b !important;
                color: #fafafa !important;
            }

            .dark-mode-support .wrapper {
                background-color: #09090b !important;
            }

            .dark-mode-support .container {
                background-color: #18181b !important;
                border-color: #27272a !important;
            }

            .dark-mode-support .header {
                background: linear-gradient(to right, #18181b 0%, #27272a 100%) !important;
                border-bottom-color: #3f3f46 !important;
            }

            .dark-mode-support .content {
                background-color: #18181b !important;
            }

            .dark-mode-support .greeting,
            .dark-mode-support .card-title,
            .dark-mode-support .header-title {
                color: #fafafa !important;
            }

            .dark-mode-support .header-subtitle {
                color: #a1a1aa !important;
            }

            .dark-mode-support .text {
                color: #d4d4d8 !important;
            }

            .dark-mode-support .text strong {
                color: #fafafa !important;
            }

            .dark-mode-support .card {
                background-color: #27272a !important;
                border-color: #3f3f46 !important;
            }

            .dark-mode-support .card-text,
            .dark-mode-support .card-meta {
                color: #a1a1aa !important;
            }

            .dark-mode-support .info-box {
                background-color: rgba(59, 130, 246, 0.1) !important;
                border-color: #3b82f6 !important;
            }

            .dark-mode-support .info-box.warning {
                background-color: rgba(245, 158, 11, 0.1) !important;
                border-color: #f59e0b !important;
            }

            .dark-mode-support .info-box.success {
                background-color: rgba(16, 185, 129, 0.1) !important;
                border-color: #10b981 !important;
            }

            .dark-mode-support .info-box-text {
                color: #d4d4d8 !important;
            }

            .dark-mode-support .footer {
                background: linear-gradient(to right, #27272a 0%, #18181b 100%) !important;
                border-top-color: #3f3f46 !important;
            }

            .dark-mode-support .footer-text {
                color: #a1a1aa !important;
            }

            .dark-mode-support .divider,
            .dark-mode-support .footer-divider {
                background-color: #3f3f46 !important;
            }

            .dark-mode-support .footer-legal {
                color: #71717a !important;
            }
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .wrapper {
                padding: 15px 10px !important;
            }

            .header {
                padding: 24px 20px !important;
            }

            .content {
                padding: 32px 20px !important;
            }

            .greeting {
                font-size: 20px !important;
            }

            .footer {
                padding: 24px 20px !important;
            }

            .button {
                display: block !important;
                width: 100% !important;
                padding: 14px 20px !important;
            }

            .card {
                padding: 20px !important;
            }

            .info-box {
                padding: 16px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Georgia, 'Times New Roman', Times, serif; background-color: #f4f4f5; color: #18181b;">

<!-- Preview Text -->
@isset($previewText)
    <div style="display: none; max-height: 0px; overflow: hidden; mso-hide: all;">
        {{ $previewText }}
    </div>
    <div style="display: none; max-height: 0px; overflow: hidden; mso-hide: all;">
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>
@endisset

<div class="wrapper" style="background-color: #f4f4f5; padding: 30px 15px;">
    <table role="presentation" class="container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; width: 100%; border-collapse: collapse; border: 1px solid #e4e4e7;">

        <!-- Header with subtle gradient -->
        <tr>
            <td class="header" style="background: linear-gradient(to right, #f4f4f5 0%, #e4e4e7 100%); padding: 32px 40px; text-align: center; border-bottom: 2px solid #d4d4d8;">
                @if($showLogo ?? true)
                    <div class="logo" style="display: inline-block; margin-bottom: 16px;">
                        <img src="{{ $logo ?? asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height: 45px; width: auto; display: block;">
                    </div>
                @endif

                @isset($headerTitle)
                    <h1 class="header-title" style="font-size: 26px; font-weight: 700; color: #18181b; margin: 0 0 8px; line-height: 1.2; font-family: Arial, Helvetica, sans-serif;">
                        {{ $headerTitle }}
                    </h1>
                @endisset

                @isset($headerSubtitle)
                    <p class="header-subtitle" style="font-size: 14px; color: #52525b; margin: 0; font-family: Arial, Helvetica, sans-serif;">
                        {{ $headerSubtitle }}
                    </p>
                @endisset
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td class="content" style="padding: 40px 40px; background-color: #ffffff;">
                {{ $slot }}
            </td>
        </tr>

        <!-- Footer with subtle gradient -->
        <tr>
            <td class="footer" style="background: linear-gradient(to right, #fafafa 0%, #f4f4f5 100%); padding: 32px 40px; text-align: center; border-top: 2px solid #e4e4e7;">
                <p class="footer-text" style="font-size: 13px; line-height: 1.6; color: #71717a; margin: 0 0 16px;">
                    You're receiving this email as a member of {{ config('app.name') }}
                </p>

                <div class="footer-links" style="margin: 16px 0;">
                    <a href="{{ config('app.url') }}" class="footer-link" style="color: #3b82f6; font-size: 13px; margin: 0 10px; text-decoration: underline; font-weight: 600;">Home</a>
                    <span style="color: #d4d4d8;">|</span>
                    <a href="{{ config('app.url') }}/help" class="footer-link" style="color: #3b82f6; font-size: 13px; margin: 0 10px; text-decoration: underline; font-weight: 600;">Help Center</a>
                    <span style="color: #d4d4d8;">|</span>
                    <a href="{{ config('app.url') }}/contact" class="footer-link" style="color: #3b82f6; font-size: 13px; margin: 0 10px; text-decoration: underline; font-weight: 600;">Contact</a>
                </div>

                <hr class="footer-divider" style="height: 1px; background-color: #e4e4e7; border: 0; margin: 20px 0;">

                <p class="footer-legal" style="font-size: 11px; color: #a1a1aa; line-height: 1.5; margin: 8px 0 0;">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    @isset($companyAddress)
                        <br>{{ $companyAddress }}
                    @endisset
                </p>

                @if($showUnsubscribe ?? false)
                    <p class="footer-legal" style="font-size: 11px; color: #a1a1aa; margin: 12px 0 0;">
                        <a href="{{ $unsubscribeUrl ?? '#' }}" style="color: #a1a1aa; text-decoration: underline;">Unsubscribe from these emails</a>
                    </p>
                @endif
            </td>
        </tr>

    </table>
</div>

</body>
</html>
