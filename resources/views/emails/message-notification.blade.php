<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Message</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 10px;
        }

        .email-container {
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.08);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .email-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .email-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M20 20c0 11.046-8.954 20-20 20s-20-8.954-20-20 8.954-20 20-20 20 8.954 20 20zm10 0c0-16.569-13.431-30-30-30s-30 13.431-30 30 13.431 30 30 30 30-13.431 30-30z'/%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-40px) translateY(-40px); }
        }

        .urgent-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 59, 48, 0.9);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
            backdrop-filter: blur(10px);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .urgent-badge::before {
            content: '⚡';
            font-size: 14px;
        }

        .email-title {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-body {
            padding: 32px 28px;
        }

        .meta-info {
            display: grid;
            gap: 16px;
            margin-bottom: 32px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 4px solid #4f46e5;
        }

        .meta-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .meta-icon {
            width: 18px;
            height: 18px;
            color: #6b7280;
            flex-shrink: 0;
        }

        .meta-label {
            font-weight: 600;
            color: #374151;
            min-width: 60px;
        }

        .meta-value {
            color: #1f2937;
            font-weight: 500;
        }

        .message-content {
            font-size: 16px;
            line-height: 1.7;
            color: #1f2937;
            margin-bottom: 32px;
        }

        .message-content p {
            margin-bottom: 16px;
        }

        .attachments {
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 32px;
            transition: all 0.3s ease;
        }

        .attachments:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .attachments-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .attachments-icon {
            width: 20px;
            height: 20px;
            color: #6b7280;
        }

        .attachments-title {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
        }

        .attachment-list {
            display: grid;
            gap: 12px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .attachment-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .attachment-icon {
            width: 16px;
            height: 16px;
            color: #6b7280;
        }

        .attachment-name {
            flex: 1;
            font-weight: 500;
            color: #1f2937;
        }

        .attachment-size {
            font-size: 14px;
            color: #6b7280;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 14px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }

        .urgent-alert {
            background: linear-gradient(135deg, #ff3b30 0%, #ff6b47 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .urgent-alert-icon {
            font-size: 20px;
            margin-top: 2px;
        }

        .urgent-alert-content strong {
            display: block;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .footer {
            background: #f8fafc;
            padding: 24px 28px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }

        .footer-logo {
            color: #4f46e5;
            font-weight: 700;
            text-decoration: none;
        }

        .footer-logo:hover {
            text-decoration: underline;
        }

        .footer-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            body {
                padding: 10px 5px;
            }

            .email-header {
                padding: 24px 20px;
            }

            .email-title {
                font-size: 20px;
            }

            .email-body {
                padding: 24px 20px;
            }

            .meta-info {
                padding: 16px;
            }

            .meta-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .attachments {
                padding: 16px;
            }

            .footer {
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- Header -->
    <div class="email-header">
        @if($userMessage->is_urgent)
            <div class="urgent-badge">Urgent Message</div>
        @endif
        <h1 class="email-title">{{ $userMessage->subject }}</h1>
    </div>

    <!-- Body -->
    <div class="email-body">
        <!-- Sender Info -->
        <div class="meta-info">
            <div class="meta-row">
                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="meta-label">From:</span>
                <span class="meta-value">{{ $userMessage->sender->name }}</span>
            </div>
            <div class="meta-row">
                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="meta-label">Sent:</span>
                <span class="meta-value">
                        {{ $userMessage->sent_at ? $userMessage->sent_at->format('F j, Y \a\t g:i A') : $userMessage->created_at->format('F j, Y \a\t g:i A') }}
                    </span>
            </div>
            <div class="meta-row">
                <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span class="meta-label">To:</span>
                <span class="meta-value">{{ $recipient->name }}</span>
            </div>
        </div>

        <!-- Message Content -->
        <div class="message-content">
            {!! $userMessage->body !!}
        </div>

        <!-- Attachments -->
        @if($userMessage->attachments && $userMessage->attachments->count() > 0)
            <div class="attachments">
                <div class="attachments-header">
                    <svg class="attachments-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <h3 class="attachments-title">Attachments ({{ $userMessage->attachments->count() }})</h3>
                </div>
                <div class="attachment-list">
                    @foreach($userMessage->attachments as $attachment)
                        <div class="attachment-item">
                            <svg class="attachment-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="attachment-name">{{ $attachment->original_filename }}</span>
                            <span class="attachment-size">
                                    @if(method_exists($attachment, 'human_readable_size'))
                                    ({{ $attachment->human_readable_size }})
                                @else
                                    ({{ number_format($attachment->size / 1024, 1) }} KB)
                                @endif
                                </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- View Online Button -->
        <div style="text-align: center;">
            <a href="{{ $messageUrl }}" class="action-button">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                View Full Message Online
            </a>
        </div>

        <!-- Urgent Alert -->
        @if($userMessage->is_urgent)
            <div class="urgent-alert">
                <span class="urgent-alert-icon">⚠️</span>
                <div class="urgent-alert-content">
                    <strong>This is an urgent message.</strong>
                    Please read and respond promptly if required.
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            This message was sent through the school management system.<br>
            <a href="{{ config('app.url') }}" class="footer-logo">{{ config('app.name') }}</a>
        </p>
        <div class="footer-divider"></div>
        <p>
            If you have trouble viewing this message, <a href="{{ $messageUrl }}">click here to view it online</a>.
        </p>
    </div>
</div>
</body>
</html>
