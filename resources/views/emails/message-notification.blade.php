<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $renderedSubject ?? $userMessage->subject }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        @media only screen and (max-width: 620px) {
            .wrapper { padding: 12px !important; }
            .card { border-radius: 0 !important; }
            .header-cell { padding: 28px 24px !important; }
            .meta-cell { padding: 12px 24px !important; }
            .body-cell { padding: 28px 24px !important; }
            .footer-cell { padding: 18px 24px !important; }
            .btn-cell { padding: 0 24px 28px 24px !important; }
            .attach-cell { padding: 0 24px 28px 24px !important; }
            .subject-text { font-size: 20px !important; }
            .meta-text { font-size: 12px !important; }
            .body-text { font-size: 15px !important; }
            .btn-link { padding: 13px 24px !important; font-size: 14px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#eef2f7;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef2f7;">
    <tr>
        <td class="wrapper" align="center" style="padding:40px 16px;">

            <!-- Card -->
            <table role="presentation" class="card" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

                <!-- Top border accent -->
                <tr>
                    <td style="background-color:{{ $userMessage->is_urgent ? '#b91c1c' : '#1d4ed8' }};height:5px;font-size:0;line-height:0;">&nbsp;</td>
                </tr>

                <!-- Header -->
                <tr>
                    <td class="header-cell" style="background-color:{{ $userMessage->is_urgent ? '#b91c1c' : '#1d4ed8' }};padding:36px 48px 32px;">

                        <!-- School name -->
                        <p style="margin:0 0 16px 0;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.6);font-family:Arial,Helvetica,sans-serif;">
                            {{ config('app.name') }}
                        </p>

                        @if($userMessage->is_urgent)
                            <!-- Urgent pill -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:14px;">
                                <tr>
                                    <td style="background-color:rgba(255,255,255,0.2);border-radius:20px;padding:4px 14px;">
                                        <span style="font-size:11px;font-weight:700;color:#ffffff;letter-spacing:1px;font-family:Arial,Helvetica,sans-serif;">⚠&nbsp; URGENT MESSAGE</span>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- Subject -->
                        <h1 class="subject-text" style="margin:0;font-size:24px;font-weight:700;color:#ffffff;line-height:1.35;font-family:Arial,Helvetica,sans-serif;">
                            {{ $renderedSubject ?? $userMessage->subject }}
                        </h1>

                    </td>
                </tr>

                <!-- Meta strip -->
                <tr>
                    <td class="meta-cell" style="background-color:#f8fafc;border-bottom:1px solid #e8edf3;padding:14px 48px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <p class="meta-text" style="margin:0;font-size:13px;color:#64748b;font-family:Arial,Helvetica,sans-serif;line-height:1.5;">
                                        <span style="color:#94a3b8;">From</span>&ensp;<strong style="color:#334155;">{{ $userMessage->sender->name }}</strong>
                                        &ensp;<span style="color:#cbd5e1;">|</span>&ensp;
                                        <span style="color:#94a3b8;">To</span>&ensp;<strong style="color:#334155;">{{ $recipient->name }}</strong>
                                        &ensp;<span style="color:#cbd5e1;">|</span>&ensp;
                                        <span style="color:#94a3b8;">
                                            {{ $userMessage->sent_at
                                                ? $userMessage->sent_at->format('M j, Y · g:i A')
                                                : $userMessage->created_at->format('M j, Y · g:i A') }}
                                        </span>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td class="body-cell" style="padding:36px 48px 32px;">
                        <p class="body-text" style="margin:0;font-size:15px;line-height:1.8;color:#334155;font-family:Georgia,'Times New Roman',serif;white-space:pre-line;">{{ $renderedBody ?? $userMessage->body }}</p>
                    </td>
                </tr>

                <!-- Attachments -->
                @if($userMessage->attachments && $userMessage->attachments->count() > 0)
                    <tr>
                        <td class="attach-cell" style="padding:0 48px 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
                                <tr>
                                    <td style="background-color:#f8fafc;padding:10px 16px;border-bottom:1px solid #e2e8f0;">
                                        <span style="font-size:12px;font-weight:700;color:#475569;letter-spacing:0.5px;font-family:Arial,Helvetica,sans-serif;text-transform:uppercase;">📎&nbsp; Attachments</span>
                                    </td>
                                </tr>
                                @foreach($userMessage->attachments as $attachment)
                                    <tr>
                                        <td style="padding:10px 16px;{{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td style="font-size:13px;color:#334155;font-family:Arial,Helvetica,sans-serif;">
                                                        {{ $attachment->original_filename }}
                                                    </td>
                                                    <td align="right" style="font-size:12px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;white-space:nowrap;">
                                                        @if(isset($attachment->size))
                                                            {{ number_format($attachment->size / 1024, 1) }} KB
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endif

                <!-- CTA -->
                <tr>
                    <td class="btn-cell" align="center" style="padding:0 48px 40px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <!--[if mso]>
                                <td style="background-color:#1d4ed8;border-radius:6px;">
                                <![endif]-->
                                <td style="background-color:#1d4ed8;border-radius:6px;">
                                    <a class="btn-link" href="{{ $messageUrl }}" style="display:inline-block;padding:14px 32px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;font-family:Arial,Helvetica,sans-serif;letter-spacing:0.2px;">
                                        View Message Online &rarr;
                                    </a>
                                </td>
                                <!--[if mso]>
                                </td>
                                <![endif]-->
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Divider -->
                <tr>
                    <td style="padding:0 48px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="border-top:1px solid #e8edf3;font-size:0;line-height:0;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td class="footer-cell" style="padding:20px 48px 28px;text-align:center;">
                        <p style="margin:0 0 6px 0;font-size:12px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;line-height:1.6;">
                            You received this message via <strong style="color:#64748b;">{{ config('app.name') }}</strong>.
                        </p>
                        <p style="margin:0;font-size:12px;color:#94a3b8;font-family:Arial,Helvetica,sans-serif;">
                            Can't view this email properly?
                            <a href="{{ $messageUrl }}" style="color:#1d4ed8;text-decoration:none;font-weight:600;">View it online</a>.
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Card -->

            <!-- Below-card note -->
            <p style="margin:20px 0 0 0;font-size:11px;color:#94a3b8;text-align:center;font-family:Arial,Helvetica,sans-serif;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>

        </td>
    </tr>
</table>

</body>
</html>
