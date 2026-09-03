@props([
    'event',
    'reminder',
    'notifiable',
    'timeText',
    'startTime',
    'eventUrl' => null,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Event Reminder: {{ $event->title }}</title>
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
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
        }
        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                margin: auto !important;
            }
            .stack-column {
                display: block !important;
                width: 100% !important;
            }
        }

        /* Rich text / Markdown content styles */
        .event-description p {
            margin: 0 0 12px 0;
            font-size: 15px;
            line-height: 1.6;
            color: #495057;
        }
        .event-description p:last-child {
            margin-bottom: 0;
        }
        .event-description h1, .event-description h2, .event-description h3,
        .event-description h4, .event-description h5, .event-description h6 {
            margin: 16px 0 8px 0;
            font-weight: 600;
            color: #212529;
            line-height: 1.3;
        }
        .event-description h1 { font-size: 20px; }
        .event-description h2 { font-size: 18px; }
        .event-description h3 { font-size: 16px; }
        .event-description h4, .event-description h5, .event-description h6 { font-size: 15px; }
        .event-description ul, .event-description ol {
            margin: 8px 0;
            padding-left: 24px;
            color: #495057;
        }
        .event-description li {
            margin: 4px 0;
            line-height: 1.5;
        }
        .event-description a {
            color: #495057;
            text-decoration: underline;
        }
        .event-description blockquote {
            margin: 12px 0;
            padding: 8px 16px;
            border-left: 3px solid #dee2e6;
            color: #6c757d;
            font-style: italic;
        }
        .event-description code {
            background-color: #f1f3f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #495057;
        }
        .event-description pre {
            background-color: #f1f3f4;
            padding: 12px;
            border-radius: 4px;
            overflow-x: auto;
            margin: 12px 0;
        }
        .event-description pre code {
            background: none;
            padding: 0;
        }
        .event-description strong, .event-description b {
            font-weight: 600;
            color: #212529;
        }
        .event-description em, .event-description i {
            font-style: italic;
        }
        .event-description hr {
            border: none;
            border-top: 1px solid #dee2e6;
            margin: 16px 0;
        }
        .event-description img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .event-description table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        .event-description th, .event-description td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .event-description th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa;">
    <!-- Preview text -->
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        Reminder: {{ $event->title }} starts in {{ $timeText }}
    </div>

    <!-- Email wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa;">
        <tr>
            <td style="padding: 40px 20px;">
                <!-- Email container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="margin: 0 auto; background-color: #ffffff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="padding: 48px 48px 32px 48px; border-bottom: 1px solid #e9ecef;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #6c757d;">
                                            Event Reminder
                                        </p>
                                        <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #212529; line-height: 1.3;">
                                            {{ $event->title }}
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px 48px;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 24px 0; font-size: 16px; line-height: 1.6; color: #495057;">
                                Hello {{ $notifiable->name }},
                            </p>

                            <!-- Main message -->
                            <p style="margin: 0 0 32px 0; font-size: 16px; line-height: 1.6; color: #495057;">
                                This is a reminder that your scheduled event begins in <strong style="color: #212529;">{{ $timeText }}</strong>.
                            </p>

                            <!-- Event details card -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 32px; background-color: #f8f9fa; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <!-- Date & Time -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 16px;">
                                            <tr>
                                                <td width="24" valign="top" style="padding-right: 12px;">
                                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E" alt="" width="20" height="20" style="display: block;">
                                                </td>
                                                <td valign="top">
                                                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d;">
                                                        When
                                                    </p>
                                                    <p style="margin: 0; font-size: 15px; color: #212529;">
                                                        {{ $startTime }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        @if($event->description)
                                        <!-- Description -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td width="24" valign="top" style="padding-right: 12px;">
                                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='17' y1='10' x2='3' y2='10'%3E%3C/line%3E%3Cline x1='21' y1='6' x2='3' y2='6'%3E%3C/line%3E%3Cline x1='21' y1='14' x2='3' y2='14'%3E%3C/line%3E%3Cline x1='17' y1='18' x2='3' y2='18'%3E%3C/line%3E%3C/svg%3E" alt="" width="20" height="20" style="display: block;">
                                                </td>
                                                <td valign="top">
                                                    <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d;">
                                                        Details
                                                    </p>
                                                    <div class="event-description" style="margin: 0; font-size: 15px; color: #495057; line-height: 1.6;">
                                                        <x-ui.latex :content="$event->description" />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if($eventUrl)
                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 32px;">
                                <tr>
                                    <td>
                                        <a href="{{ $eventUrl }}" target="_blank" style="display: inline-block; padding: 14px 28px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #212529; text-decoration: none; border-radius: 4px;">
                                            View Event Details
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Closing -->
                            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #495057;">
                                Best regards,<br>
                                <span style="color: #212529;">The {{ config('app.name') }} Team</span>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 48px; border-top: 1px solid #e9ecef; background-color: #f8f9fa; border-radius: 0 0 4px 4px;">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #6c757d; text-align: center;">
                                You received this email because you set a reminder for this event.
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #6c757d; text-align: center;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
