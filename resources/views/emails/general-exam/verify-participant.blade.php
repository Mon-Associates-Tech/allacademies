<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email</title>
    <style>
        :root {
            color-scheme: light dark;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f4f6fb;
            color: #111827;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            padding: 32px 16px 48px;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 12px 40px rgba(17, 24, 39, 0.08);
            border: 1px solid #e5e7eb;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        p {
            margin: 0 0 14px;
            line-height: 1.6;
            color: #374151;
            font-size: 15px;
        }
        .btn {
            display: inline-block;
            padding: 12px 18px;
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #fff;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);
        }
        .muted {
            color: #6b7280;
            font-size: 13px;
        }
        .link-box {
            word-break: break-all;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            font-size: 13px;
            color: #4b5563;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
        @media (prefers-color-scheme: dark) {
            body { background: #0f172a; color: #e5e7eb; }
            .card { background: #0b1220; border-color: #1f2937; box-shadow: 0 12px 40px rgba(0,0,0,0.35); }
            h1 { color: #e5e7eb; }
            p { color: #cbd5e1; }
            .muted { color: #94a3b8; }
            .link-box { background: #111827; border-color: #1f2937; color: #cbd5e1; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Verify your email</h1>
        <p>Hi {{ $participant->name ?? 'there' }},</p>
        <p>You’re almost ready to access <strong>{{ $assignment->title }}</strong>. Please confirm your email to continue.</p>
        <p style="margin: 22px 0;">
            <a href="{{ $verificationUrl }}" class="btn">Verify email</a>
        </p>
        <p class="muted" style="margin-bottom: 10px;">If that button doesn’t work, copy and paste this link:</p>
        <div class="link-box">{{ $verificationUrl }}</div>
        <p class="muted" style="margin-top: 18px;">Didn’t request this? You can ignore this email.</p>
    </div>
    <div class="footer">Sent by {{ config('app.name') }}</div>
</div>
</body>
</html>
