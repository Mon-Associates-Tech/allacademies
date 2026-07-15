<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Expired — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #080C18;
            --surface:  rgba(240, 237, 230, 0.04);
            --gold:     #D49830;
            --gold-h:   #e8aa3a;
            --text:     #F0EDE6;
            --muted:    rgba(240, 237, 230, 0.55);
            --faint:    rgba(240, 237, 230, 0.12);
            --very-faint: rgba(240, 237, 230, 0.22);
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.028) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.028) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        /* Radial glow */
        body::after {
            content: '';
            position: fixed;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 152, 48, 0.07) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }

        /* Corner accents */
        .corner {
            position: fixed;
            width: 160px;
            height: 160px;
            opacity: 0.12;
            pointer-events: none;
            z-index: 1;
        }
        .corner.tl { top: 0; left: 0; border-top: 1px solid var(--gold); border-left: 1px solid var(--gold); }
        .corner.tr { top: 0; right: 0; border-top: 1px solid var(--gold); border-right: 1px solid var(--gold); }
        .corner.bl { bottom: 0; left: 0; border-bottom: 1px solid var(--gold); border-left: 1px solid var(--gold); }
        .corner.br { bottom: 0; right: 0; border-bottom: 1px solid var(--gold); border-right: 1px solid var(--gold); }

        /* Main card */
        .card {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 540px;
            width: 100%;
            animation: rise 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Icon */
        .icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(212, 152, 48, 0.08);
            border: 1px solid rgba(212, 152, 48, 0.25);
            margin-bottom: 2rem;
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(212,152,48,0.15); }
            50%       { box-shadow: 0 0 0 14px rgba(212,152,48,0); }
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
            color: var(--gold);
        }

        /* Typography */
        .eyebrow {
            font-family: 'Syne', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px, 6vw, 40px);
            font-weight: 800;
            color: var(--text);
            line-height: 1.1;
            margin-bottom: 1.25rem;
            letter-spacing: -0.01em;
        }

        .title span { color: var(--gold); }

        .divider {
            width: 40px;
            height: 2px;
            background: var(--gold);
            opacity: 0.55;
            border-radius: 2px;
            margin: 0 auto 1.5rem;
        }

        .body-text {
            font-size: 15.5px;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.75;
            margin-bottom: 2.5rem;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }

        .body-text strong {
            color: rgba(240, 237, 230, 0.85);
            font-weight: 500;
        }

        /* Buttons */
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: var(--gold);
            color: var(--bg);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.01em;
        }
        .btn-primary:hover  { background: var(--gold-h); transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: transparent;
            color: rgba(240, 237, 230, 0.65);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
            border-radius: 6px;
            border: 1px solid var(--faint);
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.2s, color 0.2s, transform 0.15s;
        }
        .btn-secondary:hover {
            border-color: rgba(240,237,230,0.28);
            color: rgba(240,237,230,0.9);
            transform: translateY(-1px);
        }

        .btn-primary svg,
        .btn-secondary svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        /* Meta row */
        .meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            font-size: 12px;
            color: var(--very-faint);
            letter-spacing: 0.04em;
            flex-wrap: wrap;
        }

        .meta-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(240,237,230,0.2);
            flex-shrink: 0;
        }

        /* Brand footer */
        .brand {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'Syne', sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(240,237,230,0.18);
            white-space: nowrap;
        }

        .brand-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--gold);
            opacity: 0.5;
        }
    </style>
</head>
<body>

    <div class="corner tl"></div>
    <div class="corner tr"></div>
    <div class="corner bl"></div>
    <div class="corner br"></div>

    <main class="card">

        <div class="icon-wrap" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                <circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/>
            </svg>
        </div>

        <p class="eyebrow">Trial Period Ended</p>

        <h1 class="title">Your access has<br><span>expired</span></h1>

        <div class="divider"></div>

        <p class="body-text">
            The evaluation period for this platform has concluded.
            To continue using the system, please contact
            <strong>All Academies</strong> to discuss your
            licensing and acquisition options.
        </p>

        <div class="actions">
            <a href="mailto:{{ config('app.contact_email', 'allacademies2023@gmail.com') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
                Get in touch
            </a>
            <a href="{{ config('app.contact_url', 'https://allacademies.com') }}" class="btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                    <path d="M2 12h20"/>
                </svg>
                allacademies.com
            </a>
        </div>

        <div class="meta">
            <span>allacademies2023@gmail.com</span>
            <div class="meta-dot"></div>
            <span>allacademies.com</span>
            <div class="meta-dot"></div>
            <span>Ghana</span>
        </div>

    </main>

    <footer class="brand">
        <div class="brand-dot"></div>
        All Academies
         <div class="meta-dot"></div>
        Powered by Mon and Associates Tech
        <div class="brand-dot"></div>
    </footer>

</body>
</html>