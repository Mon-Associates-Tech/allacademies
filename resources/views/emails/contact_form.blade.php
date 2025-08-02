<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Contact Form Submission - {{ config('app.name') }}</title>
    <style>
        /* Reset styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background-color: #f7fafc;
            padding: 20px 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.025em;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 500;
        }

        .icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .content {
            padding: 32px 24px;
        }

        .priority-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 24px;
        }

        .field-group {
            margin-bottom: 24px;
            border-left: 3px solid #e2e8f0;
            padding-left: 16px;
        }

        .field-group.priority {
            border-left-color: #f59e0b;
        }

        .label {
            font-weight: 600;
            color: #4a5568;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: block;
        }

        .value {
            background: #f8fafc;
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 15px;
            color: #2d3748;
            word-wrap: break-word;
        }

        .value.email {
            font-family: 'Courier New', monospace;
            color: #4f46e5;
        }

        .message-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            min-height: 100px;
            font-size: 15px;
            line-height: 1.7;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .newsletter-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .newsletter-badge.yes {
            background: #d1fae5;
            color: #065f46;
        }

        .newsletter-badge.no {
            background: #f3f4f6;
            color: #6b7280;
        }

        .footer {
            background: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .timestamp {
            color: #9ca3af;
            font-size: 13px;
            font-weight: 500;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 24px 0;
        }

        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                margin: 0 10px;
                border-radius: 8px;
            }

            .header {
                padding: 24px 16px;
            }

            .header h1 {
                font-size: 20px;
            }

            .content {
                padding: 24px 16px;
            }

            .footer {
                padding: 20px 16px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a202c;
            }

            .email-wrapper {
                background: #2d3748;
            }

            .content {
                color: #e2e8f0;
            }

            .value, .message-box {
                background: #4a5568;
                border-color: #718096;
                color: #e2e8f0;
            }

            .footer {
                background: #4a5568;
                border-color: #718096;
            }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="header">
        <div class="header-content">
            <div class="icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                    <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                </svg>
            </div>
            <h1>New Contact Form Submission</h1>
            <p>{{ config('app.name') }} Website</p>
        </div>
    </div>

    <div class="content">
        @if(str_contains(strtolower($data['subject']), 'urgent') || str_contains(strtolower($data['message']), 'urgent'))
            <div class="priority-badge">High Priority</div>
        @endif

        <div class="field-group {{ str_contains(strtolower($data['subject']), 'urgent') ? 'priority' : '' }}">
            <span class="label">Contact Information</span>
            <div class="value">
                <strong>{{ $data['first_name'] }} {{ $data['last_name'] }}</strong>
            </div>
        </div>

        <div class="field-group">
            <span class="label">Email Address</span>
            <div class="value email">{{ $data['email'] }}</div>
        </div>

        <div class="field-group">
            <span class="label">Subject Category</span>
            <div class="value">{{ ucfirst(str_replace('_', ' ', $data['subject'])) }}</div>
        </div>

        <div class="field-group">
            <span class="label">Newsletter Subscription</span>
            <div class="value">
                    <span class="newsletter-badge {{ $data['newsletter'] ? 'yes' : 'no' }}">
                        {{ $data['newsletter'] ? '✓ Subscribed' : '✗ Not Subscribed' }}
                    </span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="field-group">
            <span class="label">Message Content</span>
            <div class="message-box">{{ $data['message'] }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This message was submitted through the {{ config('app.name') }} contact form</p>
        <p class="timestamp">
            Received on {{ now()->format('l, F j, Y \a\t g:i A T') }}
        </p>
    </div>
</div>
</body>
</html>
