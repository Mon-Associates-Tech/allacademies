<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
          content="AllAcademies – Ghana's most comprehensive school management and digital learning platform. Serving 12,000+ students, 500+ institutions.">
    <title>AllAcademies | Transform Education in Ghana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        /* ─────────────────────────────────────────────
           DESIGN TOKENS
        ───────────────────────────────────────────── */
        :root {
            --navy: #0B1B4D;
            --navy-mid: #162466;
            --navy-light: #1E3080;
            --amber: #F5A623;
            --amber-dark: #D48C0F;
            --amber-pale: #FEF3D7;
            --teal: #0EA882;
            --teal-pale: #E0F7F1;
            --white: #FFFFFF;
            --bg: #F7F8FC;
            --surface: #FFFFFF;
            --text-body: #2C3A5C;
            --text-muted: #6B7A9E;
            --text-hint: #9BA8C5;
            --border: #E2E8F4;
            --border-dark: #C9D3E8;

            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 32px;

            --shadow-sm: 0 1px 3px rgba(11, 27, 77, .06), 0 1px 2px rgba(11, 27, 77, .04);
            --shadow-md: 0 4px 16px rgba(11, 27, 77, .10), 0 2px 6px rgba(11, 27, 77, .06);
            --shadow-lg: 0 16px 48px rgba(11, 27, 77, .14), 0 4px 12px rgba(11, 27, 77, .08);

            --font-display: 'Playfair Display', Georgia, serif;
            --font-body: 'DM Sans', system-ui, sans-serif;

            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.25rem;
            --space-6: 1.5rem;
            --space-8: 2rem;
            --space-10: 2.5rem;
            --space-12: 3rem;
            --space-16: 4rem;
            --space-20: 5rem;
            --space-24: 6rem;
            --space-32: 8rem;

            --transition: 230ms cubic-bezier(.4, 0, .2, 1);
        }

        /* ─────────────────────────────────────────────
           RESET & BASE
        ───────────────────────────────────────────── */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.7;
            color: var(--text-body);
            background: var(--bg);
            -webkit-font-smoothing: antialiased;
        }

        img {
            display: block;
            max-width: 100%;
            height: auto;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        ul {
            list-style: none;
        }

        button {
            cursor: pointer;
            font-family: inherit;
            border: none;
            background: none;
        }

        /* Skip to content – Accessibility */
        .skip-link {
            position: absolute;
            top: -100%;
            left: var(--space-4);
            background: var(--amber);
            color: var(--navy);
            font-weight: 600;
            padding: var(--space-2) var(--space-4);
            border-radius: var(--radius-sm);
            z-index: 9999;
            transition: top var(--transition);
        }

        .skip-link:focus {
            top: var(--space-4);
        }

        /* ─────────────────────────────────────────────
           TYPOGRAPHY SCALE
        ───────────────────────────────────────────── */
        .display-1 {
            font-family: var(--font-display);
            font-size: clamp(2.4rem, 5.5vw, 4rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.02em;
            color: var(--white);
        }

        .display-2 {
            font-family: var(--font-display);
            font-size: clamp(1.75rem, 3.5vw, 2.75rem);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.015em;
            color: var(--navy);
        }

        .display-3 {
            font-family: var(--font-display);
            font-size: clamp(1.35rem, 2.5vw, 2rem);
            font-weight: 700;
            line-height: 1.25;
            color: var(--navy);
        }

        .lead {
            font-size: clamp(1.05rem, 1.8vw, 1.2rem);
            font-weight: 300;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.85);
        }

        .body-lg {
            font-size: 1.0625rem;
            line-height: 1.7;
        }

        .body-sm {
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .label {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--amber);
        }

        /* ─────────────────────────────────────────────
           LAYOUT UTILITIES
        ───────────────────────────────────────────── */
        .container {
            width: 100%;
            max-width: 1200px;
            margin-inline: auto;
            padding-inline: clamp(1.25rem, 5vw, 3rem);
        }

        .container-sm {
            max-width: 760px;
        }

        .section {
            padding-block: clamp(4rem, 8vw, 7rem);
        }

        .section-sm {
            padding-block: clamp(2.5rem, 5vw, 4rem);
        }

        .grid-2 {
            display: grid;
            gap: var(--space-8);
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .grid-3 {
            display: grid;
            gap: var(--space-6);
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .grid-4 {
            display: grid;
            gap: var(--space-5);
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .flex-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .text-center {
            text-align: center;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* ─────────────────────────────────────────────
           BUTTONS
        ───────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: 0.75rem 1.75rem;
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            font-weight: 600;
            line-height: 1;
            transition: transform var(--transition), box-shadow var(--transition), background var(--transition);
            white-space: nowrap;
            text-decoration: none;
        }

        .btn:focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 3px;
        }

        .btn-primary {
            background: var(--amber);
            color: var(--navy);
            box-shadow: 0 4px 14px rgba(245, 166, 35, .35);
        }

        .btn-primary:hover {
            background: var(--amber-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 166, 35, .4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, .1);
            color: var(--white);
            border: 1.5px solid rgba(255, 255, 255, .35);
            backdrop-filter: blur(8px);
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, .2);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--navy);
            border: 1.5px solid var(--border-dark);
        }

        .btn-outline:hover {
            border-color: var(--navy);
            transform: translateY(-2px);
        }

        .btn-teal {
            background: var(--teal);
            color: var(--white);
            box-shadow: 0 4px 14px rgba(14, 168, 130, .3);
        }

        .btn-teal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 168, 130, .4);
        }

        .btn-lg {
            padding: 1rem 2.25rem;
            font-size: 1.0625rem;
        }

        .btn-sm {
            padding: 0.5rem 1.1rem;
            font-size: 0.8125rem;
        }

        /* ─────────────────────────────────────────────
           BADGE / TAG
        ───────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.3rem 0.85rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.03em;
        }

        .badge-amber {
            background: var(--amber-pale);
            color: #8B5D00;
        }

        .badge-teal {
            background: var(--teal-pale);
            color: #0A6E54;
        }

        .badge-navy {
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .9);
            border: 1px solid rgba(255, 255, 255, .2);
        }

        /* ─────────────────────────────────────────────
           NAVIGATION
        ───────────────────────────────────────────── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 0;
            transition: background var(--transition), box-shadow var(--transition);
        }

        .navbar.scrolled {
            background: rgba(11, 27, 77, .97);
            backdrop-filter: blur(16px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, .2);
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
            padding-inline: clamp(1.25rem, 5vw, 3rem);
            max-width: 1200px;
            margin-inline: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            flex-shrink: 0;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--amber) 0%, #E8920B 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--navy);
            flex-shrink: 0;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .logo-name {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--white);
        }

        .logo-tagline {
            font-size: 0.65rem;
            font-weight: 400;
            color: rgba(255, 255, 255, .6);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: var(--space-1);
        }

        .nav-link {
            padding: var(--space-2) var(--space-3);
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, .8);
            border-radius: var(--radius-sm);
            transition: color var(--transition), background var(--transition);
            white-space: nowrap;
        }

        .nav-link:hover {
            color: var(--white);
            background: rgba(255, 255, 255, .08);
        }

        .nav-link:focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 2px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .nav-signin {
            font-size: 0.9rem;
            font-weight: 500;
            color: rgba(255, 255, 255, .85);
            padding: var(--space-2) var(--space-3);
            border-radius: var(--radius-sm);
            transition: color var(--transition);
        }

        .nav-signin:hover {
            color: var(--white);
        }

        /* Hamburger */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            width: 36px;
            height: 36px;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            padding: var(--space-2);
            background: rgba(255, 255, 255, .08);
        }

        .hamburger span {
            display: block;
            width: 20px;
            height: 2px;
            background: var(--white);
            border-radius: 2px;
            transition: transform var(--transition), opacity var(--transition);
        }

        .hamburger.open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .hamburger.open span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Mobile nav */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: var(--navy-mid);
            z-index: 99;
            padding: var(--space-4);
            transform: translateY(-100%);
            opacity: 0;
            transition: transform var(--transition), opacity var(--transition);
            pointer-events: none;
        }

        .mobile-nav.open {
            display: block;
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-nav a {
            display: block;
            padding: var(--space-3) var(--space-4);
            color: rgba(255, 255, 255, .85);
            font-weight: 500;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            transition: color var(--transition);
        }

        .mobile-nav a:hover {
            color: var(--amber);
        }

        .mobile-nav-actions {
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
            margin-top: var(--space-4);
            padding-top: var(--space-4);
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        /* ─────────────────────────────────────────────
           ANNOUNCEMENT BAR
        ───────────────────────────────────────────── */
        .announcement {
            background: var(--amber);
            color: var(--navy);
            text-align: center;
            padding: var(--space-2) var(--space-4);
            font-size: 0.8125rem;
            font-weight: 600;
            position: relative;
            z-index: 101;
        }

        .announcement a {
            text-decoration: underline;
            margin-left: var(--space-2);
        }

        /* ─────────────────────────────────────────────
           HERO SECTION
        ───────────────────────────────────────────── */
        .hero {
            min-height: 100svh;
            background: var(--navy);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-block: 8rem 5rem;
        }

        /* Subtle geometric background */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 70% at 65% 40%, rgba(14, 168, 130, .12) 0%, transparent 60%),
            radial-gradient(ellipse 60% 60% at 20% 80%, rgba(245, 166, 35, .08) 0%, transparent 55%),
            radial-gradient(ellipse 50% 50% at 80% 15%, rgba(30, 48, 128, .6) 0%, transparent 60%);
            pointer-events: none;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-16);
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content {
        }

        .hero-label {
            margin-bottom: var(--space-5);
        }

        .hero-title {
            margin-bottom: var(--space-6);
        }

        .hero-title em {
            font-style: normal;
            color: var(--amber);
        }

        .hero-desc {
            margin-bottom: var(--space-8);
            max-width: 44ch;
        }

        .hero-ctas {
            display: flex;
            gap: var(--space-4);
            flex-wrap: wrap;
            align-items: center;
        }

        .hero-trust {
            margin-top: var(--space-8);
            display: flex;
            align-items: center;
            gap: var(--space-4);
            flex-wrap: wrap;
        }

        .trust-avatars {
            display: flex;
        }

        .trust-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: 2px solid var(--navy);
            margin-left: -10px;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .trust-avatar:first-child {
            margin-left: 0;
        }

        .trust-avatar:nth-child(2) {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .trust-avatar:nth-child(3) {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .trust-avatar:nth-child(4) {
            background: linear-gradient(135deg, var(--teal), #059669);
        }

        .trust-text {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, .7);
            line-height: 1.4;
        }

        .trust-text strong {
            color: var(--white);
            display: block;
        }

        /* Hero visual panel */
        .hero-visual {
            position: relative;
        }

        .dashboard-mockup {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: var(--radius-lg);
            padding: var(--space-4);
            backdrop-filter: blur(12px);
            position: relative;
        }

        .dash-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-4);
        }

        .dash-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .9);
        }

        .dash-dots {
            display: flex;
            gap: 5px;
        }

        .dash-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .dash-dot:nth-child(1) {
            background: #ff5f57;
        }

        .dash-dot:nth-child(2) {
            background: #febc2e;
        }

        .dash-dot:nth-child(3) {
            background: #28c840;
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--space-3);
            margin-bottom: var(--space-4);
        }

        .stat-box {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: var(--radius-md);
            padding: var(--space-3);
            text-align: center;
        }

        .stat-box-num {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--white);
            line-height: 1;
            margin-bottom: 2px;
        }

        .stat-box-lbl {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, .5);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .chart-area {
            background: rgba(255, 255, 255, .04);
            border-radius: var(--radius-md);
            padding: var(--space-3);
            margin-bottom: var(--space-4);
        }

        .chart-label {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, .5);
            margin-bottom: var(--space-2);
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 60px;
        }

        .chart-bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: rgba(14, 168, 130, .4);
            transition: background var(--transition);
        }

        .chart-bar.active {
            background: var(--teal);
        }

        .dash-list {
            display: flex;
            flex-direction: column;
            gap: var(--space-2);
        }

        .dash-item {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            background: rgba(255, 255, 255, .04);
            border-radius: var(--radius-sm);
            padding: var(--space-2) var(--space-3);
        }

        .dash-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--amber);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--navy);
            flex-shrink: 0;
        }

        .dash-item-info {
            flex: 1;
        }

        .dash-item-name {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .85);
        }

        .dash-item-sub {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, .45);
        }

        .dash-item-badge {
            font-size: 0.6rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 100px;
        }

        .dash-item-badge.green {
            background: rgba(14, 168, 130, .2);
            color: var(--teal);
        }

        .dash-item-badge.amber {
            background: rgba(245, 166, 35, .2);
            color: var(--amber);
        }

        /* Floating cards on hero */
        .hero-float {
            position: absolute;
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: var(--space-3) var(--space-4);
            font-size: 0.8rem;
        }

        .hero-float-1 {
            top: -20px;
            left: -30px;
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .hero-float-2 {
            bottom: -16px;
            right: -16px;
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }

        .float-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .float-icon.green {
            background: var(--teal-pale);
        }

        .float-icon.amber {
            background: var(--amber-pale);
        }

        .float-text strong {
            display: block;
            font-size: 0.875rem;
            color: var(--navy);
            font-weight: 700;
        }

        .float-text span {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        /* ─────────────────────────────────────────────
           STATS BAR
        ───────────────────────────────────────────── */
        .stats-bar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: relative;
            z-index: 2;
        }

        .stats-inner {
            display: flex;
            align-items: stretch;
            flex-wrap: wrap;
            max-width: 1200px;
            margin-inline: auto;
        }

        .stat-item {
            flex: 1;
            min-width: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: var(--space-6) var(--space-4);
            border-right: 1px solid var(--border);
            text-align: center;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-num {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
            color: var(--navy);
            line-height: 1;
            margin-bottom: var(--space-1);
        }

        .stat-num span {
            color: var(--amber);
        }

        .stat-lbl {
            font-size: 0.8125rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ─────────────────────────────────────────────
           SECTION HEADER
        ───────────────────────────────────────────── */
        .section-header {
            text-align: center;
            margin-bottom: clamp(2rem, 4vw, 4rem);
        }

        .section-header .label {
            margin-bottom: var(--space-3);
        }

        .section-header p {
            color: var(--text-muted);
            max-width: 52ch;
            margin-inline: auto;
            margin-top: var(--space-4);
            font-size: 1.05rem;
        }

        /* ─────────────────────────────────────────────
           FEATURES CARDS
        ───────────────────────────────────────────── */
        .features-bg {
            background: var(--bg);
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: var(--space-8) var(--space-6);
            transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--amber), var(--teal));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-dark);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: var(--space-5);
        }

        .feature-icon.amber {
            background: var(--amber-pale);
        }

        .feature-icon.teal {
            background: var(--teal-pale);
        }

        .feature-icon.navy {
            background: rgba(11, 27, 77, .07);
        }

        .feature-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: var(--space-3);
        }

        .feature-desc {
            font-size: 0.9375rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .feature-tags {
            margin-top: var(--space-5);
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-2);
        }

        .feature-tag {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 100px;
            background: var(--bg);
            color: var(--text-body);
            border: 1px solid var(--border);
        }

        /* ─────────────────────────────────────────────
           USER TYPES SECTION
        ───────────────────────────────────────────── */
        .user-types-bg {
            background: var(--white);
        }

        .user-tab-list {
            display: flex;
            gap: var(--space-2);
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: var(--space-10);
        }

        .user-tab {
            padding: var(--space-2) var(--space-5);
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1.5px solid var(--border);
            color: var(--text-muted);
            transition: all var(--transition);
            background: var(--white);
            cursor: pointer;
        }

        .user-tab:hover {
            border-color: var(--navy);
            color: var(--navy);
        }

        .user-tab.active {
            background: var(--navy);
            color: var(--white);
            border-color: var(--navy);
        }

        .user-tab:focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 2px;
        }

        .user-panel {
            display: none;
        }

        .user-panel.active {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-12);
            align-items: center;
        }

        .user-panel-content {
        }

        .user-panel-img {
            background: var(--bg);
            border-radius: var(--radius-lg);
            overflow: hidden;
            aspect-ratio: 4/3;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            position: relative;
        }

        .user-visual {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: var(--space-6);
            gap: var(--space-3);
        }

        .user-stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--space-3);
        }

        .user-stat {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: var(--space-4);
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .user-stat-n {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--navy);
            line-height: 1;
        }

        .user-stat-l {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .user-feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
            margin-top: var(--space-6);
        }

        .user-feature-item {
            display: flex;
            align-items: flex-start;
            gap: var(--space-3);
            padding: var(--space-4) var(--space-4);
            background: var(--bg);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }

        .check-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--teal-pale);
            color: var(--teal);
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
            font-weight: 700;
        }

        .user-feature-item p {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 2px;
        }

        .user-feature-item small {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ─────────────────────────────────────────────
           CTA SECTION
        ───────────────────────────────────────────── */
        .cta-section {
            background: var(--navy);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 70% 80% at 50% 50%, rgba(14, 168, 130, .15) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-inner {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .cta-inner .display-2 {
            color: var(--white);
            margin-bottom: var(--space-4);
        }

        .cta-inner .lead {
            color: rgba(255, 255, 255, .75);
            margin-bottom: var(--space-8);
        }

        .cta-inner .lead a {
            color: var(--amber);
            text-decoration: underline;
        }

        .cta-actions {
            display: flex;
            gap: var(--space-4);
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-note {
            margin-top: var(--space-5);
            font-size: 0.8rem;
            color: rgba(255, 255, 255, .45);
        }

        /* ─────────────────────────────────────────────
           MODULES GRID
        ───────────────────────────────────────────── */
        .modules-bg {
            background: var(--bg);
        }

        .module-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: var(--space-6);
            display: flex;
            flex-direction: column;
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .module-num {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--amber);
            margin-bottom: var(--space-3);
        }

        .module-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: var(--space-3);
        }

        .module-desc {
            font-size: 0.875rem;
            color: var(--text-muted);
            flex: 1;
            margin-bottom: var(--space-5);
        }

        .module-features {
            display: flex;
            flex-direction: column;
            gap: var(--space-2);
        }

        .module-feature {
            display: flex;
            align-items: center;
            gap: var(--space-2);
            font-size: 0.8125rem;
            color: var(--text-body);
        }

        .module-feature::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--teal);
            flex-shrink: 0;
        }

        /* ─────────────────────────────────────────────
           TESTIMONIALS
        ───────────────────────────────────────────── */
        .testimonials-bg {
            background: var(--white);
        }

        .testimonial-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: var(--space-7) var(--space-6);
            position: relative;
            overflow: hidden;
        }

        .testimonial-card::before {
            content: '\201C';
            position: absolute;
            top: -8px;
            left: 16px;
            font-family: var(--font-display);
            font-size: 6rem;
            line-height: 1;
            color: var(--amber);
            opacity: 0.15;
            pointer-events: none;
        }

        .testimonial-text {
            font-size: 0.975rem;
            line-height: 1.75;
            color: var(--text-body);
            margin-bottom: var(--space-5);
            position: relative;
            z-index: 1;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: var(--space-3);
        }

        .t-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .t-name {
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--navy);
        }

        .t-role {
            font-size: 0.775rem;
            color: var(--text-muted);
        }

        .star-row {
            display: flex;
            gap: 2px;
            margin-bottom: var(--space-4);
        }

        .star {
            color: var(--amber);
            font-size: 0.875rem;
        }

        /* ─────────────────────────────────────────────
           FAQ
        ───────────────────────────────────────────── */
        .faq-bg {
            background: var(--bg);
        }

        .faq-list {
            max-width: 700px;
            margin-inline: auto;
            display: flex;
            flex-direction: column;
            gap: var(--space-3);
        }

        .faq-item {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            transition: box-shadow var(--transition);
        }

        .faq-item.open {
            box-shadow: var(--shadow-sm);
        }

        .faq-q {
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-5) var(--space-6);
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--navy);
            gap: var(--space-4);
            background: none;
            border: none;
            cursor: pointer;
        }

        .faq-q:focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: -2px;
        }

        .faq-arrow {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform var(--transition), background var(--transition);
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .faq-item.open .faq-arrow {
            transform: rotate(180deg);
            background: var(--navy);
            color: var(--white);
        }

        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding var(--transition);
            padding: 0 var(--space-6);
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.75;
        }

        .faq-item.open .faq-a {
            max-height: 300px;
            padding: 0 var(--space-6) var(--space-5);
        }

        /* ─────────────────────────────────────────────
           NEWSLETTER SECTION
        ───────────────────────────────────────────── */
        .newsletter {
            background: var(--white);
            border-top: 1px solid var(--border);
        }

        .newsletter-inner {
            text-align: center;
            max-width: 540px;
            margin-inline: auto;
        }

        .newsletter-inner .display-3 {
            margin-bottom: var(--space-3);
        }

        .newsletter-inner p {
            color: var(--text-muted);
            margin-bottom: var(--space-6);
        }

        .newsletter-form {
            display: flex;
            gap: var(--space-3);
            flex-wrap: wrap;
            justify-content: center;
        }

        .nl-input {
            flex: 1;
            min-width: 220px;
            padding: 0.75rem 1.25rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 0.9375rem;
            font-family: inherit;
            color: var(--text-body);
            background: var(--bg);
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .nl-input::placeholder {
            color: var(--text-hint);
        }

        .nl-input:focus {
            outline: none;
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(11, 27, 77, .08);
        }

        .nl-note {
            margin-top: var(--space-3);
            font-size: 0.775rem;
            color: var(--text-hint);
        }

        /* ─────────────────────────────────────────────
           FOOTER
        ───────────────────────────────────────────── */
        footer {
            background: var(--navy);
            color: rgba(255, 255, 255, .7);
        }

        .footer-top {
            padding-block: var(--space-16) var(--space-10);
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr);
            gap: var(--space-8);
        }

        .footer-brand .logo-name {
            color: var(--white);
            font-size: 1.1rem;
        }

        .footer-brand p {
            font-size: 0.875rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, .55);
            margin-top: var(--space-4);
            max-width: 26ch;
        }

        .footer-contact {
            margin-top: var(--space-5);
            font-size: 0.8rem;
        }

        .footer-contact a {
            color: rgba(255, 255, 255, .7);
            display: block;
            margin-top: var(--space-2);
            transition: color var(--transition);
        }

        .footer-contact a:hover {
            color: var(--amber);
        }

        .footer-col h4 {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .45);
            margin-bottom: var(--space-4);
        }

        .footer-col a {
            display: block;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, .65);
            margin-bottom: var(--space-3);
            transition: color var(--transition);
        }

        .footer-col a:hover {
            color: var(--amber);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding-block: var(--space-5);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--space-4);
            flex-wrap: wrap;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, .4);
        }

        .footer-bottom-links {
            display: flex;
            gap: var(--space-5);
        }

        .footer-bottom-links a {
            color: rgba(255, 255, 255, .4);
            transition: color var(--transition);
        }

        .footer-bottom-links a:hover {
            color: rgba(255, 255, 255, .75);
        }

        /* ─────────────────────────────────────────────
           SCROLL ANIMATIONS
        ───────────────────────────────────────────── */
        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .stagger-1 {
            transition-delay: 0.05s;
        }

        .stagger-2 {
            transition-delay: 0.12s;
        }

        .stagger-3 {
            transition-delay: 0.19s;
        }

        .stagger-4 {
            transition-delay: 0.26s;
        }

        .stagger-5 {
            transition-delay: 0.33s;
        }

        /* ─────────────────────────────────────────────
           RESPONSIVE
        ───────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .footer-top {
                grid-template-columns: 1fr 1fr;
            }

            .hero-grid {
                grid-template-columns: 1fr;
                gap: var(--space-12);
            }

            .hero {
                padding-block: 7rem 4rem;
            }

            .hero-desc {
                max-width: 100%;
            }

            .hero-visual {
                order: -1;
            }

            .hero-float-1, .hero-float-2 {
                display: none;
            }

            .user-panel.active {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-links, .nav-actions .btn, .nav-signin {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            .footer-top {
                grid-template-columns: 1fr 1fr;
            }

            .stats-inner {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .stat-item {
                min-width: 50%;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .stat-item:nth-child(odd) {
                border-right: 1px solid var(--border);
            }

            .stat-item:last-child {
                border-bottom: none;
            }

            .user-tab-list {
                gap: var(--space-2);
            }

            .cta-actions {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 560px) {
            .footer-top {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }

            .hero-ctas {
                flex-direction: column;
            }

            .hero-ctas .btn {
                width: 100%;
                justify-content: center;
            }

            .grid-4 {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* ─────────────────────────────────────────────
           FOCUS MANAGEMENT
        ───────────────────────────────────────────── */
        :focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 2px;
            border-radius: 2px;
        }

        /* ─────────────────────────────────────────────
           CINEMATIC VIDEO HERO
        ───────────────────────────────────────────── */
        .video-hero {
            position: relative;
            width: 100%;
            height: 100svh;
            min-height: 600px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* The actual background video */
        .vh-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
            transform: scale(1.04);
            transition: transform 8s ease-out;
        }

        .vh-video.loaded {
            transform: scale(1);
        }

        /* Multi-layer overlay system */
        .vh-overlay-base {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(
                160deg,
                rgba(6, 12, 38, .82) 0%,
                rgba(11, 27, 77, .70) 40%,
                rgba(5, 30, 40, .75) 100%
            );
        }

        /* Amber vignette at bottom */
        .vh-overlay-vignette {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: radial-gradient(ellipse 100% 60% at 50% 100%, rgba(245, 166, 35, .18) 0%, transparent 65%),
            radial-gradient(ellipse 60% 40% at 0% 0%, rgba(14, 168, 130, .12) 0%, transparent 55%);
        }

        /* Diagonal light streak */
        .vh-overlay-streak {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: linear-gradient(118deg, rgba(255, 255, 255, .04) 0%, transparent 45%);
        }

        /* Content sits above all overlays */
        .vh-content {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
            padding-inline: clamp(1.25rem, 5vw, 3rem);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-16);
            align-items: center;
        }

        /* Left column: text */
        .vh-text {
        }

        .vh-eyebrow {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            margin-bottom: var(--space-6);
            opacity: 0;
            animation: vhFadeUp 0.7s ease 0.2s forwards;
        }

        .vh-eyebrow-line {
            width: 36px;
            height: 2px;
            background: linear-gradient(90deg, var(--amber), transparent);
            flex-shrink: 0;
        }

        .vh-eyebrow-text {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--amber);
        }

        .vh-headline {
            font-family: var(--font-display);
            font-size: clamp(2.6rem, 5.5vw, 4.25rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.025em;
            color: var(--white);
            margin-bottom: var(--space-6);
            opacity: 0;
            animation: vhFadeUp 0.8s ease 0.35s forwards;
        }

        .vh-headline .accent {
            position: relative;
            color: var(--amber);
            display: inline-block;
        }

        .vh-headline .accent::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--amber), var(--teal));
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: left;
            animation: underlineGrow 0.6s ease 1.2s forwards;
        }

        @keyframes underlineGrow {
            to {
                transform: scaleX(1);
            }
        }

        .vh-sub {
            font-size: clamp(1rem, 1.6vw, 1.15rem);
            font-weight: 300;
            line-height: 1.75;
            color: rgba(255, 255, 255, .75);
            max-width: 44ch;
            margin-bottom: var(--space-8);
            opacity: 0;
            animation: vhFadeUp 0.8s ease 0.5s forwards;
        }

        .vh-ctas {
            display: flex;
            gap: var(--space-4);
            flex-wrap: wrap;
            align-items: center;
            opacity: 0;
            animation: vhFadeUp 0.8s ease 0.65s forwards;
        }

        /* Play button CTA */
        .vh-play-btn {
            display: inline-flex;
            align-items: center;
            gap: var(--space-3);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--white);
            font-family: var(--font-body);
            font-size: 0.9375rem;
            font-weight: 600;
            padding: 0;
            transition: gap var(--transition);
        }

        .vh-play-btn:hover {
            gap: var(--space-4);
        }

        .vh-play-btn:hover .play-ring {
            border-color: var(--amber);
        }

        .vh-play-btn:hover .play-triangle {
            border-left-color: var(--amber);
        }

        .play-ring {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .5);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: border-color var(--transition), transform var(--transition);
            position: relative;
        }

        .play-ring::before {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .15);
            animation: pulse-ring 2.5s ease infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            70% {
                transform: scale(1.35);
                opacity: 0;
            }
            100% {
                opacity: 0;
            }
        }

        .play-triangle {
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-left: 14px solid white;
            margin-left: 3px;
            transition: border-left-color var(--transition);
        }

        /* Right column: floating overlay cards */
        .vh-cards {
            position: relative;
            height: 420px;
            opacity: 0;
            animation: vhFadeUp 1s ease 0.8s forwards;
        }

        .vh-card {
            position: absolute;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--radius-lg);
            padding: var(--space-4) var(--space-5);
            color: var(--white);
        }

        .vh-card-main {
            width: 100%;
            top: 0;
            left: 0;
            right: 0;
            padding: var(--space-5);
        }

        .vh-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: var(--space-4);
        }

        .vh-card-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .9);
        }

        .vh-live-dot {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--teal);
            letter-spacing: 0.05em;
        }

        .vh-live-dot::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--teal);
            animation: liveBlip 1.5s ease infinite;
        }

        @keyframes liveBlip {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.3;
            }
        }

        .vh-mini-bars {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            height: 48px;
            margin-bottom: var(--space-4);
        }

        .vh-bar {
            flex: 1;
            border-radius: 3px 3px 0 0;
            background: rgba(255, 255, 255, .12);
        }

        .vh-bar.hi {
            background: var(--teal);
        }

        .vh-bar.md {
            background: rgba(14, 168, 130, .5);
        }

        .vh-card-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--space-3);
        }

        .vh-mini-stat {
            text-align: center;
        }

        .vh-mini-num {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--white);
            line-height: 1;
        }

        .vh-mini-lbl {
            font-size: 0.6rem;
            color: rgba(255, 255, 255, .45);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 2px;
        }

        .vh-card-pill {
            bottom: 60px;
            right: -20px;
            display: flex;
            align-items: center;
            gap: var(--space-3);
            padding: var(--space-3) var(--space-4);
            animation: floatCard 4s ease-in-out infinite;
        }

        @keyframes floatCard {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        .vh-pill-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .vh-pill-icon.amber {
            background: rgba(245, 166, 35, .25);
        }

        .vh-pill-text strong {
            display: block;
            font-size: 0.875rem;
            color: var(--white);
            font-weight: 700;
            line-height: 1;
        }

        .vh-pill-text span {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, .5);
        }

        .vh-card-badge {
            top: 130px;
            right: -24px;
            display: flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-2) var(--space-3);
            animation: floatCard 3.5s ease-in-out 0.5s infinite;
        }

        .vh-rating-stars {
            font-size: 0.65rem;
            color: var(--amber);
            letter-spacing: 1px;
        }

        .vh-badge-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--white);
        }

        .vh-badge-sub {
            font-size: 0.6rem;
            color: rgba(255, 255, 255, .5);
        }

        /* Scroll indicator */
        .vh-scroll {
            position: absolute;
            bottom: var(--space-8);
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-2);
            opacity: 0;
            animation: vhFadeUp 0.7s ease 1.4s forwards;
        }

        .vh-scroll-text {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .4);
        }

        .vh-scroll-line {
            width: 1px;
            height: 44px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, .35), transparent);
            position: relative;
            overflow: hidden;
        }

        .vh-scroll-line::after {
            content: '';
            position: absolute;
            top: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, transparent, var(--amber));
            animation: scrollDrop 2s ease infinite;
        }

        @keyframes scrollDrop {
            0% {
                top: -100%;
            }
            100% {
                top: 100%;
            }
        }

        /* Horizontal band of floating text marquee */
        .vh-marquee-wrap {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 8;
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, .07);
            background: rgba(0, 0, 0, .25);
            backdrop-filter: blur(8px);
            padding: var(--space-3) 0;
        }

        .vh-marquee {
            display: flex;
            gap: var(--space-10);
            width: max-content;
            animation: marqueeRoll 28s linear infinite;
        }

        .vh-marquee:hover {
            animation-play-state: paused;
        }

        @keyframes marqueeRoll {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-50%);
            }
        }

        .vh-marquee-item {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            font-size: 0.775rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .55);
            white-space: nowrap;
            letter-spacing: 0.04em;
        }

        .vh-marquee-item::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--amber);
            flex-shrink: 0;
        }

        /* Fade-up keyframe for video hero */
        @keyframes vhFadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Video modal */
        .vh-modal {
            position: fixed;
            inset: 0;
            z-index: 9000;
            background: rgba(0, 0, 0, .88);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-6);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(6px);
        }

        .vh-modal.open {
            opacity: 1;
            pointer-events: auto;
        }

        .vh-modal-inner {
            width: 100%;
            max-width: 900px;
            position: relative;
        }

        .vh-modal video {
            width: 100%;
            border-radius: var(--radius-lg);
            aspect-ratio: 16/9;
            background: #000;
            display: block;
        }

        .vh-modal-close {
            position: absolute;
            top: -48px;
            right: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            color: var(--white);
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background var(--transition);
        }

        .vh-modal-close:hover {
            background: rgba(255, 255, 255, .2);
        }

        /* Responsive video hero */
        @media (max-width: 900px) {
            .vh-content {
                grid-template-columns: 1fr;
                gap: var(--space-8);
            }

            .vh-cards {
                display: none;
            }

            .vh-headline {
                font-size: clamp(2.2rem, 7vw, 3rem);
            }
        }

        @media (max-width: 560px) {
            .vh-ctas {
                flex-direction: column;
            }

            .vh-ctas .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* ─────────────────────────────────────────────
           VIDEO DEMO SECTION
        ───────────────────────────────────────────── */
        .demo-bg {
            background: var(--navy);
        }

        .demo-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-16);
            align-items: center;
        }

        .demo-text-side {
        }

        .demo-text-side .label {
            margin-bottom: var(--space-3);
        }

        .demo-text-side .display-2 {
            color: var(--white);
            margin-bottom: var(--space-5);
        }

        .demo-text-side p {
            color: rgba(255, 255, 255, .65);
            font-size: 1.05rem;
            line-height: 1.75;
            margin-bottom: var(--space-8);
        }

        .demo-bullets {
            display: flex;
            flex-direction: column;
            gap: var(--space-4);
        }

        .demo-bullet {
            display: flex;
            align-items: flex-start;
            gap: var(--space-3);
            padding: var(--space-4);
            background: rgba(255, 255, 255, .04);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: var(--radius-md);
            transition: background var(--transition), border-color var(--transition);
        }

        .demo-bullet:hover {
            background: rgba(255, 255, 255, .07);
            border-color: rgba(255, 255, 255, .14);
        }

        .demo-bullet-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: rgba(245, 166, 35, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .demo-bullet-icon.teal {
            background: rgba(14, 168, 130, .15);
        }

        .demo-bullet strong {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 2px;
        }

        .demo-bullet span {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, .5);
        }

        /* Video player widget */
        .demo-player-side {
        }

        .demo-player {
            position: relative;
            border-radius: var(--radius-lg);
            overflow: hidden;
            aspect-ratio: 16/9;
            background: #000;
            cursor: pointer;
            box-shadow: 0 32px 80px rgba(0, 0, 0, .5), 0 0 0 1px rgba(255, 255, 255, .08);
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .demo-player:hover {
            transform: scale(1.015);
            box-shadow: 0 40px 100px rgba(0, 0, 0, .6), 0 0 0 1px rgba(245, 166, 35, .3);
        }

        .demo-player:hover .demo-play-bg {
            transform: scale(1.1);
            background: rgba(245, 166, 35, .95);
        }

        .demo-player:hover .demo-play-triangle {
            border-left-color: var(--navy);
        }

        .demo-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.65);
            transition: filter var(--transition);
        }

        .demo-player:hover .demo-thumb {
            filter: brightness(0.5);
        }

        .demo-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(11, 27, 77, .6) 0%, transparent 50%);
        }

        .demo-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-3);
        }

        .demo-play-bg {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(245, 166, 35, .85);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform var(--transition), background var(--transition);
            box-shadow: 0 0 0 12px rgba(245, 166, 35, .15);
        }

        .demo-play-triangle {
            width: 0;
            height: 0;
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
            border-left: 20px solid var(--white);
            margin-left: 4px;
            transition: border-left-color var(--transition);
        }

        .demo-play-label {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-shadow: 0 1px 6px rgba(0, 0, 0, .5);
        }

        .demo-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: var(--space-4) var(--space-5);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .demo-caption-left {
        }

        .demo-caption-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--white);
        }

        .demo-caption-sub {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, .55);
        }

        .demo-caption-dur {
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(0, 0, 0, .4);
            color: rgba(255, 255, 255, .8);
            padding: 3px 8px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255, 255, 255, .1);
        }

        /* Progress bar decoration */
        .demo-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, .12);
        }

        .demo-progress-fill {
            height: 100%;
            width: 35%;
            background: linear-gradient(90deg, var(--amber), var(--teal));
            border-radius: 0 2px 2px 0;
        }

        @media (max-width: 900px) {
            .demo-inner {
                grid-template-columns: 1fr;
                gap: var(--space-10);
            }

            .demo-player-side {
                order: -1;
            }
        }
    </style>
</head>
<body>

<!-- Skip link for keyboard / screen-reader users -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<!-- ─── ANNOUNCEMENT ─────────────────────────── -->
<div class="announcement" role="banner" aria-label="Site announcement">
    🎉 New: Advanced AI Assessment Tools now live —
    <a href="https://allacademies.com/features" aria-label="Learn more about AI Assessment Tools">Learn more →</a>
</div>

<!-- ─── NAVBAR ───────────────────────────────── -->
<nav class="navbar" role="navigation" aria-label="Primary navigation" id="navbar">
    <div class="navbar-inner">
        <a href="https://allacademies.com" class="logo" aria-label="AllAcademies home">
            <div class="logo-icon" aria-hidden="true">AA</div>
            <div class="logo-text">
                <span class="logo-name">AllAcademies</span>
                <span class="logo-tagline">Educational Excellence</span>
            </div>
        </a>

        <ul class="nav-links" role="list" aria-label="Site sections">
            <li><a href="https://allacademies.com/features" class="nav-link">Features</a></li>
            <li><a href="https://allacademies.com/library" class="nav-link">Library</a></li>
            <li><a href="https://allacademies.com/pricing" class="nav-link">Pricing</a></li>
            <li><a href="https://allacademies.com/financial-aid-programs" class="nav-link">Financial Aid</a></li>
            <li><a href="https://allacademies.com/contact" class="nav-link">Contact</a></li>
        </ul>

        <div class="nav-actions">
            <a href="https://allacademies.com/login" class="nav-signin" aria-label="Sign in to your account">Sign In</a>
            <a href="https://allacademies.com/register" class="btn btn-primary" aria-label="Get started for free">Get
                Started Free</a>
        </div>

        <button class="hamburger" id="menuToggle" aria-expanded="false" aria-controls="mobileNav"
                aria-label="Open navigation menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav" aria-hidden="true" role="navigation" aria-label="Mobile navigation">
    <a href="https://allacademies.com/features">Features</a>
    <a href="https://allacademies.com/library">Library</a>
    <a href="https://allacademies.com/pricing">Pricing</a>
    <a href="https://allacademies.com/financial-aid-programs">Financial Aid</a>
    <a href="https://allacademies.com/contact">Contact</a>
    <div class="mobile-nav-actions">
        <a href="https://allacademies.com/login" class="btn btn-ghost" style="width:100%;justify-content:center;">Sign
            In</a>
        <a href="https://allacademies.com/register" class="btn btn-primary" style="width:100%;justify-content:center;">Get
            Started Free</a>
    </div>
</div>

<!-- ─── MAIN CONTENT ──────────────────────────── -->
<main id="main-content">

    <!-- ─── CINEMATIC VIDEO HERO ─────────────────── -->
    <section class="video-hero" aria-labelledby="vh-heading">

        <!-- Background video -->
        <video
            class="vh-video"
            src="https://allacademies.com/media/video/header-background-video.mp4"
            autoplay muted loop playsinline
            aria-hidden="true"
            preload="auto"
        ></video>

        <!-- Overlay layers -->
        <div class="vh-overlay-base" aria-hidden="true"></div>
        <div class="vh-overlay-vignette" aria-hidden="true"></div>
        <div class="vh-overlay-streak" aria-hidden="true"></div>

        <!-- Main content grid -->
        <div class="vh-content">

            <!-- Left: text -->
            <div class="vh-text">
                <div class="vh-eyebrow" aria-hidden="true">
                    <div class="vh-eyebrow-line"></div>
                    <span class="vh-eyebrow-text">Ghana's Most Trusted EdTech Platform</span>
                </div>

                <h1 class="vh-headline" id="vh-heading">
                    Where <span class="accent">Knowledge</span><br>
                    Meets<br>Possibility
                </h1>

                <p class="vh-sub">
                    AllAcademies connects 12,796 students, teachers, and institutions across Ghana — with digital
                    libraries, AI-powered assessments, real-time dashboards, and so much more.
                </p>

                <div class="vh-ctas">
                    <a href="https://allacademies.com/register" class="btn btn-primary btn-lg">
                        Start for Free
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.75"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <button class="vh-play-btn" id="vhPlayBtn" aria-label="Watch platform demo video">
            <span class="play-ring" aria-hidden="true">
              <span class="play-triangle"></span>
            </span>
                        Watch Demo
                    </button>
                </div>
            </div>

            <!-- Right: floating glass cards -->
            <div class="vh-cards" aria-hidden="true">

                <!-- Main glass dashboard card -->
                <div class="vh-card vh-card-main">
                    <div class="vh-card-header">
                        <span class="vh-card-title">Live School Overview</span>
                        <span class="vh-live-dot">LIVE</span>
                    </div>
                    <div class="vh-mini-bars">
                        <div class="vh-bar md" style="height:42%"></div>
                        <div class="vh-bar md" style="height:60%"></div>
                        <div class="vh-bar hi" style="height:74%"></div>
                        <div class="vh-bar md" style="height:55%"></div>
                        <div class="vh-bar hi" style="height:88%"></div>
                        <div class="vh-bar hi" style="height:96%"></div>
                        <div class="vh-bar md" style="height:70%"></div>
                        <div class="vh-bar" style="height:45%"></div>
                        <div class="vh-bar md" style="height:63%"></div>
                    </div>
                    <div class="vh-card-stats">
                        <div class="vh-mini-stat">
                            <div class="vh-mini-num">847</div>
                            <div class="vh-mini-lbl">Students</div>
                        </div>
                        <div class="vh-mini-stat">
                            <div class="vh-mini-num" style="color:var(--teal)">93%</div>
                            <div class="vh-mini-lbl">Attendance</div>
                        </div>
                        <div class="vh-mini-stat">
                            <div class="vh-mini-num" style="color:var(--amber)">4.9</div>
                            <div class="vh-mini-lbl">Rating</div>
                        </div>
                    </div>
                </div>

                <!-- Floating pill: books -->
                <div class="vh-card vh-card-pill">
                    <div class="vh-pill-icon amber">📚</div>
                    <div class="vh-pill-text">
                        <strong>15,000+ Books</strong>
                        <span>Updated daily</span>
                    </div>
                </div>

                <!-- Floating pill: rating -->
                <div class="vh-card vh-card-badge">
                    <div>
                        <div class="vh-rating-stars">★★★★★</div>
                        <div class="vh-badge-text">Educators love it</div>
                        <div class="vh-badge-sub">500+ institutions onboarded</div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Marquee strip -->
        <div class="vh-marquee-wrap" aria-hidden="true">
            <div class="vh-marquee">
                <span class="vh-marquee-item">Digital Library — 15,000+ Books</span>
                <span class="vh-marquee-item">AI-Powered Self Assessment</span>
                <span class="vh-marquee-item">Real-Time School Dashboard</span>
                <span class="vh-marquee-item">School Fees Payment Portal</span>
                <span class="vh-marquee-item">BECE & WASSCE Prep Tools</span>
                <span class="vh-marquee-item">Teacher Video Lessons</span>
                <span class="vh-marquee-item">500+ Institutions Onboarded</span>
                <span class="vh-marquee-item">Parent & Student Portals</span>
                <span class="vh-marquee-item">Author Book Marketplace</span>
                <!-- Duplicated for seamless loop -->
                <span class="vh-marquee-item">Digital Library — 15,000+ Books</span>
                <span class="vh-marquee-item">AI-Powered Self Assessment</span>
                <span class="vh-marquee-item">Real-Time School Dashboard</span>
                <span class="vh-marquee-item">School Fees Payment Portal</span>
                <span class="vh-marquee-item">BECE & WASSCE Prep Tools</span>
                <span class="vh-marquee-item">Teacher Video Lessons</span>
                <span class="vh-marquee-item">500+ Institutions Onboarded</span>
                <span class="vh-marquee-item">Parent & Student Portals</span>
                <span class="vh-marquee-item">Author Book Marketplace</span>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="vh-scroll" aria-hidden="true">
            <span class="vh-scroll-text">Scroll</span>
            <div class="vh-scroll-line"></div>
        </div>

    </section>

    <!-- Video Modal -->
    <div class="vh-modal" id="vhModal" role="dialog" aria-modal="true" aria-label="Platform demo video">
        <div class="vh-modal-inner">
            <button class="vh-modal-close" id="vhModalClose" aria-label="Close video">✕</button>
            <video
                id="vhModalVideo"
                src="https://allacademies.com/media/video/platform-demo.mp4"
                controls
                preload="none"
                poster="https://allacademies.com/images/students-crowded-around-computer.jpeg"
            ></video>
        </div>
    </div>

    <!-- ─── HERO ──────────────────────────────────── -->
    <section class="hero" aria-labelledby="hero-heading">
        <div class="container">
            <div class="hero-grid">

                <div class="hero-content">
                    <div class="hero-label fade-up">
                        <span class="badge badge-navy">🇬🇭 Ghana's Leading EdTech Platform</span>
                    </div>
                    <h1 class="display-1 hero-title fade-up stagger-1" id="hero-heading">
                        Education,<br><em>Reimagined</em><br>for Ghana
                    </h1>
                    <p class="lead hero-desc fade-up stagger-2">
                        The all-in-one platform connecting 12,796+ students, teachers, and institutions — with digital
                        libraries, AI-powered assessments, and real-time performance analytics.
                    </p>
                    <div class="hero-ctas fade-up stagger-3">
                        <a href="https://allacademies.com/register" class="btn btn-primary btn-lg">
                            Start Free Trial
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.75"
                                      stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <a href="https://allacademies.com/features" class="btn btn-ghost btn-lg">
                            Explore Features
                        </a>
                    </div>
                    <div class="hero-trust fade-up stagger-4">
                        <div class="trust-avatars" aria-hidden="true">
                            <div class="trust-avatar">AO</div>
                            <div class="trust-avatar">KM</div>
                            <div class="trust-avatar">SB</div>
                            <div class="trust-avatar">EA</div>
                        </div>
                        <div class="trust-text">
                            <strong>12,796 learners trust us</strong>
                            Rated 4.9/5 by educators across Ghana
                        </div>
                    </div>
                </div>

                <!-- Dashboard Mockup -->
                <div class="hero-visual fade-up stagger-2" aria-hidden="true">
                    <div class="hero-float hero-float-1">
                        <div class="float-icon green">📚</div>
                        <div class="float-text">
                            <strong>15,000+</strong>
                            <span>Digital books</span>
                        </div>
                    </div>

                    <div class="dashboard-mockup" role="img" aria-label="Platform dashboard preview">
                        <div class="dash-header">
                            <div class="dash-dots">
                                <div class="dash-dot"></div>
                                <div class="dash-dot"></div>
                                <div class="dash-dot"></div>
                            </div>
                            <span class="dash-title">School Dashboard</span>
                            <span style="font-size:.65rem;color:rgba(255,255,255,.4);">Term 2 · 2025</span>
                        </div>
                        <div class="stat-row">
                            <div class="stat-box">
                                <div class="stat-box-num">847</div>
                                <div class="stat-box-lbl">Students</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-box-num">42</div>
                                <div class="stat-box-lbl">Teachers</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-box-num" style="color:var(--teal)">93%</div>
                                <div class="stat-box-lbl">Attendance</div>
                            </div>
                        </div>
                        <div class="chart-area">
                            <div class="chart-label">Assessment Scores — Last 7 Weeks</div>
                            <div class="chart-bars">
                                <div class="chart-bar" style="height:55%"></div>
                                <div class="chart-bar" style="height:68%"></div>
                                <div class="chart-bar" style="height:72%"></div>
                                <div class="chart-bar" style="height:60%"></div>
                                <div class="chart-bar" style="height:80%"></div>
                                <div class="chart-bar active" style="height:88%"></div>
                                <div class="chart-bar" style="height:76%"></div>
                            </div>
                        </div>
                        <div class="dash-list">
                            <div class="dash-item">
                                <div class="dash-avatar">KA</div>
                                <div class="dash-item-info">
                                    <div class="dash-item-name">Kwame Asante</div>
                                    <div class="dash-item-sub">Submitted: Final Exam · JHS 3</div>
                                </div>
                                <span class="dash-item-badge green">Passed</span>
                            </div>
                            <div class="dash-item">
                                <div class="dash-avatar" style="background:var(--teal)">AB</div>
                                <div class="dash-item-info">
                                    <div class="dash-item-name">Abena Boateng</div>
                                    <div class="dash-item-sub">Library request · 2 books</div>
                                </div>
                                <span class="dash-item-badge amber">Pending</span>
                            </div>
                        </div>
                    </div>

                    <div class="hero-float hero-float-2">
                        <div class="float-icon amber">🏫</div>
                        <div class="float-text">
                            <strong>500+ Schools</strong>
                            <span>Onboarded & active</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ─── STATS BAR ─────────────────────────────── -->
    <div class="stats-bar" role="region" aria-label="Platform statistics">
        <div class="stats-inner">
            <div class="stat-item fade-up">
                <div class="stat-num" aria-label="12,796 active users"><span id="stat-users">12,796</span></div>
                <div class="stat-lbl">Active Users</div>
            </div>
            <div class="stat-item fade-up stagger-1">
                <div class="stat-num" aria-label="500+ institutions"><span id="stat-schools">500</span><span>+</span>
                </div>
                <div class="stat-lbl">Institutions</div>
            </div>
            <div class="stat-item fade-up stagger-2">
                <div class="stat-num" aria-label="15,000+ digital books"><span>15k</span><span>+</span></div>
                <div class="stat-lbl">Digital Books</div>
            </div>
            <div class="stat-item fade-up stagger-3">
                <div class="stat-num" aria-label="98% satisfaction rate"><span>98</span><span style="font-size:1.25rem">%</span>
                </div>
                <div class="stat-lbl">Satisfaction Rate</div>
            </div>
            <div class="stat-item fade-up stagger-4">
                <div class="stat-num" aria-label="50+ platform features"><span>50</span><span>+</span></div>
                <div class="stat-lbl">Platform Features</div>
            </div>
        </div>
    </div>

    <!-- ─── FEATURES ──────────────────────────────── -->
    <section class="section features-bg" id="features" aria-labelledby="features-heading">
        <div class="container">
            <div class="section-header">
                <span class="label">Platform Features</span>
                <h2 class="display-2" id="features-heading">Everything You Need to Excel</h2>
                <p>Built for the full education journey — from administration to assessment, learning to lending.</p>
            </div>

            <div class="grid-3">
                <div class="feature-card fade-up">
                    <div class="feature-icon amber" aria-hidden="true">📖</div>
                    <h3 class="feature-title">Digital Library</h3>
                    <p class="feature-desc">Access 15,000+ curated books, textbooks, research papers, and educational
                        resources — searchable, downloadable, and updated daily.</p>
                    <div class="feature-tags">
                        <span class="feature-tag">AI-Powered Search</span>
                        <span class="feature-tag">Offline Access</span>
                        <span class="feature-tag">Multiple Formats</span>
                    </div>
                </div>
                <div class="feature-card fade-up stagger-1">
                    <div class="feature-icon teal" aria-hidden="true">🎯</div>
                    <h3 class="feature-title">AI Self-Assessment</h3>
                    <p class="feature-desc">Intelligent quizzes, auto-generated exams, and adaptive performance
                        analytics that help students identify and close learning gaps.</p>
                    <div class="feature-tags">
                        <span class="feature-tag">Auto-Grading</span>
                        <span class="feature-tag">BECE/WASSCE Prep</span>
                        <span class="feature-tag">Analytics</span>
                    </div>
                </div>
                <div class="feature-card fade-up stagger-2">
                    <div class="feature-icon navy" aria-hidden="true">🏫</div>
                    <h3 class="feature-title">School ERP</h3>
                    <p class="feature-desc">Complete institutional management — enrollment, ID cards, terminal reports,
                        financial management, and event notifications in one dashboard.</p>
                    <div class="feature-tags">
                        <span class="feature-tag">Enrollment</span>
                        <span class="feature-tag">Finance</span>
                        <span class="feature-tag">Reporting</span>
                    </div>
                </div>
                <div class="feature-card fade-up">
                    <div class="feature-icon amber" aria-hidden="true">🎥</div>
                    <h3 class="feature-title">Video Teaching</h3>
                    <p class="feature-desc">Empower teachers to record lessons, upload notes, and deliver interactive
                        live or async sessions — perfect for classroom or remote learning.</p>
                    <div class="feature-tags">
                        <span class="feature-tag">Live Streaming</span>
                        <span class="feature-tag">Async Classes</span>
                        <span class="feature-tag">Forums</span>
                    </div>
                </div>
                <div class="feature-card fade-up stagger-1">
                    <div class="feature-icon teal" aria-hidden="true">💳</div>
                    <h3 class="feature-title">Fees Payment Portal</h3>
                    <p class="feature-desc">Parents pay school fees securely online. Instant receipts, payment history,
                        and direct reconciliation for school accounts — no manual processing.</p>
                    <div class="feature-tags">
                        <span class="feature-tag">Secure Payments</span>
                        <span class="feature-tag">Instant Receipts</span>
                        <span class="feature-tag">Reconciliation</span>
                    </div>
                </div>
                <div class="feature-card fade-up stagger-2">
                    <div class="feature-icon navy" aria-hidden="true">📊</div>
                    <h3 class="feature-title">Performance Monitoring</h3>
                    <p class="feature-desc">Real-time dashboards tracking student engagement, teacher activity, and
                        school-wide performance. Identify trends instantly with custom reports.</p>
                    <div class="feature-tags">
                        <span class="feature-tag">Real-time Data</span>
                        <span class="feature-tag">Custom Reports</span>
                        <span class="feature-tag">Trend Analysis</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── VIDEO DEMO ───────────────────────────── -->
    <section class="section demo-bg" id="demo" aria-labelledby="demo-heading">
        <div class="container">
            <div class="demo-inner">

                <!-- Left: text & bullets -->
                <div class="demo-text-side fade-up">
                    <span class="label">See It in Action</span>
                    <h2 class="display-2" id="demo-heading" style="margin-top:var(--space-3)">
                        A Platform Tour Worth<br>Taking
                    </h2>
                    <p>
                        See how AllAcademies brings together school management, digital learning, AI assessments, and
                        the library — all inside one intuitive platform. The tour takes under 3 minutes.
                    </p>
                    <div class="demo-bullets">
                        <div class="demo-bullet">
                            <div class="demo-bullet-icon">📊</div>
                            <div>
                                <strong>Live school dashboard</strong>
                                <span>Real-time attendance, grades, and fee tracking at a glance</span>
                            </div>
                        </div>
                        <div class="demo-bullet">
                            <div class="demo-bullet-icon teal">🎯</div>
                            <div>
                                <strong>AI-powered assessments</strong>
                                <span>Watch the quiz engine adapt questions based on student performance</span>
                            </div>
                        </div>
                        <div class="demo-bullet">
                            <div class="demo-bullet-icon">📚</div>
                            <div>
                                <strong>Digital library in action</strong>
                                <span>Browse, borrow, and read from 15,000+ educational resources</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: video player -->
                <div class="demo-player-side fade-up stagger-2">
                    <div
                        class="demo-player"
                        id="demoPlayer"
                        role="button"
                        tabindex="0"
                        aria-label="Play AllAcademies platform demo — approximately 3 minutes"
                    >
                        <img
                            class="demo-thumb"
                            src="https://allacademies.com/images/students-crowded-around-computer.jpeg"
                            alt="Students gathered around a laptop, exploring the AllAcademies platform"
                            loading="lazy"
                        >
                        <div class="demo-overlay" aria-hidden="true"></div>
                        <div class="demo-play-btn" aria-hidden="true">
                            <div class="demo-play-bg">
                                <div class="demo-play-triangle"></div>
                            </div>
                            <span class="demo-play-label">Watch Demo</span>
                        </div>
                        <div class="demo-caption">
                            <div class="demo-caption-left">
                                <div class="demo-caption-title">AllAcademies Platform Tour</div>
                                <div class="demo-caption-sub">Full walkthrough · All modules covered</div>
                            </div>
                            <span class="demo-caption-dur">2:58</span>
                        </div>
                        <div class="demo-progress" aria-hidden="true">
                            <div class="demo-progress-fill"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ─── MODULES ───────────────────────────────── -->
    <section class="section modules-bg" id="modules" aria-labelledby="modules-heading">
        <div class="container">
            <div class="section-header">
                <span class="label">Platform Modules</span>
                <h2 class="display-2" id="modules-heading">Five Modules. One Platform.</h2>
                <p>An integrated ecosystem covering every aspect of educational management.</p>
            </div>
            <div class="grid-3">
                <div class="module-card fade-up">
                    <div class="module-num">Module 01</div>
                    <h3 class="module-title">Records + Reports + ERP</h3>
                    <p class="module-desc">Enterprise resource planning for complete institutional management.</p>
                    <div class="module-features">
                        <div class="module-feature">Staff & Student Enrollment</div>
                        <div class="module-feature">ID Card Generation</div>
                        <div class="module-feature">Exam Records & Terminal Reports</div>
                        <div class="module-feature">Financial Management</div>
                    </div>
                </div>
                <div class="module-card fade-up stagger-1">
                    <div class="module-num">Module 02</div>
                    <h3 class="module-title">Teaching & Assessment</h3>
                    <p class="module-desc">AI-powered teaching tools with analytics for smarter instruction.</p>
                    <div class="module-features">
                        <div class="module-feature">Video / Audio Teaching</div>
                        <div class="module-feature">Online Quizzes & Exams</div>
                        <div class="module-feature">Auto-Generated Assessments</div>
                        <div class="module-feature">Performance Analytics</div>
                    </div>
                </div>
                <div class="module-card fade-up stagger-2">
                    <div class="module-num">Module 03</div>
                    <h3 class="module-title">Library + Book Management</h3>
                    <p class="module-desc">Complete digital and physical library management system.</p>
                    <div class="module-features">
                        <div class="module-feature">Digital & Physical Books</div>
                        <div class="module-feature">Online Reading Platform</div>
                        <div class="module-feature">Borrowing & Return System</div>
                        <div class="module-feature">Smart Notifications</div>
                    </div>
                </div>
                <div class="module-card fade-up">
                    <div class="module-num">Module 04</div>
                    <h3 class="module-title">Author Books + Subscriptions</h3>
                    <p class="module-desc">Marketplace for Ghanaian authors with flexible subscription models.</p>
                    <div class="module-features">
                        <div class="module-feature">Private Author Submissions</div>
                        <div class="module-feature">Subscription & Purchase Options</div>
                        <div class="module-feature">Free NGO / Government Books</div>
                        <div class="module-feature">Revenue Tracking</div>
                    </div>
                </div>
                <div class="module-card fade-up stagger-1">
                    <div class="module-num">Module 05</div>
                    <h3 class="module-title">Performance Monitoring</h3>
                    <p class="module-desc">Advanced analytics and tracking for comprehensive insights.</p>
                    <div class="module-features">
                        <div class="module-feature">Quiz & Assignment Records</div>
                        <div class="module-feature">School-wide Performance Trends</div>
                        <div class="module-feature">Teacher Activity Dashboards</div>
                        <div class="module-feature">Custom Reports</div>
                    </div>
                </div>
                <div class="module-card fade-up stagger-2" style="background:var(--navy);border-color:var(--navy);">
                    <div class="module-num" style="color:var(--amber)">8+ User Types</div>
                    <h3 class="module-title" style="color:var(--white)">Built for Everyone</h3>
                    <p class="module-desc" style="color:rgba(255,255,255,.65)">Tailored dashboards for every stakeholder
                        in the education ecosystem.</p>
                    <div class="module-features" style="margin-top:auto;">
                        <div class="module-feature" style="color:rgba(255,255,255,.75)">Administrators</div>
                        <div class="module-feature" style="color:rgba(255,255,255,.75)">Teachers & Students</div>
                        <div class="module-feature" style="color:rgba(255,255,255,.75)">Parents & Librarians</div>
                        <div class="module-feature" style="color:rgba(255,255,255,.75)">Authors & General Public</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── USER TYPES ────────────────────────────── -->
    <section class="section user-types-bg" id="solutions" aria-labelledby="user-types-heading">
        <div class="container">
            <div class="section-header">
                <span class="label">Built for Every Role</span>
                <h2 class="display-2" id="user-types-heading">Your Role, Your Dashboard</h2>
                <p>Tailored experiences for every stakeholder — from school heads to students.</p>
            </div>

            <div class="user-tab-list" role="tablist" aria-label="User role tabs">
                <button class="user-tab active" role="tab" aria-selected="true" aria-controls="panel-admin"
                        data-panel="admin">Administrators
                </button>
                <button class="user-tab" role="tab" aria-selected="false" aria-controls="panel-teacher"
                        data-panel="teacher">Teachers
                </button>
                <button class="user-tab" role="tab" aria-selected="false" aria-controls="panel-student"
                        data-panel="student">Students
                </button>
                <button class="user-tab" role="tab" aria-selected="false" aria-controls="panel-parent"
                        data-panel="parent">Parents
                </button>
            </div>

            <!-- Admin Panel -->
            <div class="user-panel active" id="panel-admin" role="tabpanel" aria-labelledby="tab-admin">
                <div class="user-panel-content">
                    <span class="label">School Administrators</span>
                    <h3 class="display-3" style="margin-block:var(--space-3) var(--space-5)">Run your school from a
                        single dashboard</h3>
                    <p style="color:var(--text-muted);max-width:48ch">Manage enrollments, assignments, fees, and
                        performance — all in real-time, without switching between systems.</p>
                    <ul class="user-feature-list" aria-label="Administrator features">
                        <li class="user-feature-item">
                            <div class="check-icon" aria-hidden="true">✓</div>
                            <div><p>Enrollment Management</p><small>Streamline student and staff registration with
                                    digital workflows</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon" aria-hidden="true">✓</div>
                            <div><p>Financial Oversight</p><small>Track fee collection, generate receipts, and view
                                    real-time accounts</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon" aria-hidden="true">✓</div>
                            <div><p>Performance Monitoring</p><small>School-wide analytics and teacher activity reports
                                    at a glance</small></div>
                        </li>
                    </ul>
                    <div style="margin-top:var(--space-6)">
                        <a href="https://allacademies.com/solutions/schools" class="btn btn-primary">Explore Admin
                            Features →</a>
                    </div>
                </div>
                <div class="user-panel-img" aria-hidden="true">
                    <div class="user-visual">
                        <div class="user-stat-row">
                            <div class="user-stat">
                                <div class="user-stat-n" style="color:var(--navy)">847</div>
                                <div class="user-stat-l">Students</div>
                            </div>
                            <div class="user-stat">
                                <div class="user-stat-n" style="color:var(--teal)">93%</div>
                                <div class="user-stat-l">Attendance</div>
                            </div>
                            <div class="user-stat">
                                <div class="user-stat-n" style="color:var(--amber)">GH₵</div>
                                <div class="user-stat-l">Fees Paid</div>
                            </div>
                        </div>
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-4);border:1px solid var(--border);flex:1;display:flex;flex-direction:column;gap:var(--space-3)">
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <span
                                    style="font-size:.8rem;font-weight:700;color:var(--navy)">Recent Enrollments</span>
                                <span class="badge badge-teal" style="font-size:.65rem">Live</span>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div
                                    style="display:flex;align-items:center;gap:10px;padding:8px;background:var(--bg);border-radius:8px">
                                    <div
                                        style="width:28px;height:28px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:white;flex-shrink:0">
                                        KA
                                    </div>
                                    <div style="flex:1">
                                        <div style="font-size:.75rem;font-weight:600;color:var(--navy)">Kwame Asante
                                        </div>
                                        <div style="font-size:.65rem;color:var(--text-muted)">JHS 2 · Science</div>
                                    </div>
                                    <span
                                        style="font-size:.6rem;font-weight:600;padding:2px 6px;border-radius:100px;background:var(--teal-pale);color:#0A6E54">New</span>
                                </div>
                                <div
                                    style="display:flex;align-items:center;gap:10px;padding:8px;background:var(--bg);border-radius:8px">
                                    <div
                                        style="width:28px;height:28px;border-radius:50%;background:var(--teal);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:white;flex-shrink:0">
                                        AB
                                    </div>
                                    <div style="flex:1">
                                        <div style="font-size:.75rem;font-weight:600;color:var(--navy)">Abena Boateng
                                        </div>
                                        <div style="font-size:.65rem;color:var(--text-muted)">JHS 3 · Arts</div>
                                    </div>
                                    <span
                                        style="font-size:.6rem;font-weight:600;padding:2px 6px;border-radius:100px;background:var(--amber-pale);color:#8B5D00">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Panel -->
            <div class="user-panel" id="panel-teacher" role="tabpanel" aria-labelledby="tab-teacher">
                <div class="user-panel-content">
                    <span class="label">Teachers</span>
                    <h3 class="display-3" style="margin-block:var(--space-3) var(--space-5)">Teach smarter, not
                        harder</h3>
                    <p style="color:var(--text-muted);max-width:48ch">Plan lessons, create quizzes, record video
                        lessons, and track every student's progress — all without leaving the platform.</p>
                    <ul class="user-feature-list">
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Lesson Planning & Video Upload</p><small>Create, organize, and share lessons with
                                    ease</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Quiz & Exam Builder</p><small>Auto-graded assessments with instant student
                                    feedback</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Student Performance Tracking</p><small>See individual and class-wide progress in
                                    real time</small></div>
                        </li>
                    </ul>
                    <div style="margin-top:var(--space-6)"><a href="https://allacademies.com/solutions/teachers"
                                                              class="btn btn-primary">Explore Teacher Tools →</a></div>
                </div>
                <div class="user-panel-img" aria-hidden="true">
                    <div class="user-visual">
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-4);border:1px solid var(--border)">
                            <div style="font-size:.8rem;font-weight:700;color:var(--navy);margin-bottom:var(--space-3)">
                                My Classes — Term 2
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div
                                    style="background:var(--bg);border-radius:8px;padding:10px;display:flex;justify-content:space-between;align-items:center">
                                    <span
                                        style="font-size:.8rem;font-weight:600;color:var(--navy)">Mathematics JHS 3</span><span
                                        style="font-size:.7rem;color:var(--teal);font-weight:600">34 students</span>
                                </div>
                                <div
                                    style="background:var(--bg);border-radius:8px;padding:10px;display:flex;justify-content:space-between;align-items:center">
                                    <span style="font-size:.8rem;font-weight:600;color:var(--navy)">Science JHS 2</span><span
                                        style="font-size:.7rem;color:var(--teal);font-weight:600">29 students</span>
                                </div>
                                <div
                                    style="background:var(--bg);border-radius:8px;padding:10px;display:flex;justify-content:space-between;align-items:center">
                                    <span style="font-size:.8rem;font-weight:600;color:var(--navy)">English JHS 1</span><span
                                        style="font-size:.7rem;color:var(--teal);font-weight:600">38 students</span>
                                </div>
                            </div>
                        </div>
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-4);border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div style="font-size:.75rem;color:var(--text-muted)">Latest Quiz Results</div>
                                <div style="font-size:1.5rem;font-weight:800;color:var(--navy)">78%</div>
                                <div style="font-size:.7rem;color:var(--teal)">Class Average ↑4%</div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-end">
                                <div
                                    style="height:8px;width:60px;background:var(--border);border-radius:4px;overflow:hidden">
                                    <div style="height:100%;width:78%;background:var(--teal);border-radius:4px"></div>
                                </div>
                                <div
                                    style="height:8px;width:60px;background:var(--border);border-radius:4px;overflow:hidden">
                                    <div style="height:100%;width:65%;background:var(--amber);border-radius:4px"></div>
                                </div>
                                <div
                                    style="height:8px;width:60px;background:var(--border);border-radius:4px;overflow:hidden">
                                    <div style="height:100%;width:88%;background:var(--teal);border-radius:4px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Panel -->
            <div class="user-panel" id="panel-student" role="tabpanel" aria-labelledby="tab-student">
                <div class="user-panel-content">
                    <span class="label">Students</span>
                    <h3 class="display-3" style="margin-block:var(--space-3) var(--space-5)">Learn at your pace,
                        anywhere</h3>
                    <p style="color:var(--text-muted);max-width:48ch">Access your classes, library, quizzes, and
                        BECE/WASSCE preparation tools — from any device, on any connection.</p>
                    <ul class="user-feature-list">
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Digital Library Access</p><small>15,000+ books and resources at your
                                    fingertips</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>AI Self-Assessment</p><small>Practice exams that adapt to your level</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Discussion Forums</p><small>Ask questions and collaborate with classmates</small>
                            </div>
                        </li>
                    </ul>
                    <div style="margin-top:var(--space-6)"><a href="https://allacademies.com/solutions/students"
                                                              class="btn btn-primary">Explore Student Features →</a>
                    </div>
                </div>
                <div class="user-panel-img" aria-hidden="true">
                    <div class="user-visual">
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-4);border:1px solid var(--border)">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--space-3)">
                                <div
                                    style="width:36px;height:36px;border-radius:50%;background:var(--amber);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:var(--navy)">
                                    EA
                                </div>
                                <div>
                                    <div style="font-size:.8rem;font-weight:700;color:var(--navy)">Esi Ackah</div>
                                    <div style="font-size:.65rem;color:var(--text-muted)">JHS 3 · Term 2</div>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                                <div
                                    style="background:var(--teal-pale);border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.25rem;font-weight:800;color:var(--teal)">82%</div>
                                    <div style="font-size:.65rem;color:#0A6E54">Avg. Score</div>
                                </div>
                                <div
                                    style="background:var(--amber-pale);border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.25rem;font-weight:800;color:#8B5D00">12</div>
                                    <div style="font-size:.65rem;color:#8B5D00">Books Read</div>
                                </div>
                            </div>
                        </div>
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-4);border:1px solid var(--border);flex:1">
                            <div
                                style="font-size:.75rem;font-weight:700;color:var(--navy);margin-bottom:var(--space-3)">
                                Upcoming Assignments
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px">
                                <div
                                    style="display:flex;gap:8px;align-items:center;padding:8px;background:var(--bg);border-radius:8px">
                                    <div
                                        style="width:4px;height:36px;background:var(--teal);border-radius:4px;flex-shrink:0"></div>
                                    <div>
                                        <div style="font-size:.75rem;font-weight:600;color:var(--navy)">Science Quiz
                                        </div>
                                        <div style="font-size:.65rem;color:var(--text-muted)">Due: Tomorrow</div>
                                    </div>
                                </div>
                                <div
                                    style="display:flex;gap:8px;align-items:center;padding:8px;background:var(--bg);border-radius:8px">
                                    <div
                                        style="width:4px;height:36px;background:var(--amber);border-radius:4px;flex-shrink:0"></div>
                                    <div>
                                        <div style="font-size:.75rem;font-weight:600;color:var(--navy)">Maths
                                            Assignment
                                        </div>
                                        <div style="font-size:.65rem;color:var(--text-muted)">Due: Friday</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent Panel -->
            <div class="user-panel" id="panel-parent" role="tabpanel" aria-labelledby="tab-parent">
                <div class="user-panel-content">
                    <span class="label">Parents</span>
                    <h3 class="display-3" style="margin-block:var(--space-3) var(--space-5)">Stay connected to your
                        child's education</h3>
                    <p style="color:var(--text-muted);max-width:48ch">Monitor performance, pay school fees online, and
                        receive real-time event notifications — all from your phone.</p>
                    <ul class="user-feature-list">
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Ward's Performance Reports</p><small>View terminal reports and quiz scores any
                                    time</small></div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Online Fees Payment</p><small>Pay school fees securely with instant receipt</small>
                            </div>
                        </li>
                        <li class="user-feature-item">
                            <div class="check-icon">✓</div>
                            <div><p>Event Notifications</p><small>Never miss a school event, PTA meeting, or
                                    deadline</small></div>
                        </li>
                    </ul>
                    <div style="margin-top:var(--space-6)"><a href="https://allacademies.com/general/pay/init"
                                                              class="btn btn-primary">Pay School Fees →</a></div>
                </div>
                <div class="user-panel-img" aria-hidden="true">
                    <div class="user-visual">
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-4);border:1px solid var(--border)">
                            <div style="font-size:.8rem;font-weight:700;color:var(--navy);margin-bottom:var(--space-3)">
                                Maame Boateng — Parent
                            </div>
                            <div style="display:flex;gap:10px;margin-bottom:var(--space-3)">
                                <div
                                    style="flex:1;background:var(--teal-pale);border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.1rem;font-weight:800;color:var(--teal)">A</div>
                                    <div style="font-size:.65rem;color:#0A6E54">Term Grade</div>
                                </div>
                                <div
                                    style="flex:1;background:var(--amber-pale);border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.1rem;font-weight:800;color:#8B5D00">95%</div>
                                    <div style="font-size:.65rem;color:#8B5D00">Attendance</div>
                                </div>
                                <div
                                    style="flex:1;background:rgba(11,27,77,.05);border-radius:8px;padding:10px;text-align:center">
                                    <div style="font-size:1.1rem;font-weight:800;color:var(--navy)">4th</div>
                                    <div style="font-size:.65rem;color:var(--text-muted)">Class Rank</div>
                                </div>
                            </div>
                            <div
                                style="background:var(--teal-pale);border-radius:8px;padding:10px;display:flex;align-items:center;gap:8px">
                                <span style="font-size:1rem">✅</span>
                                <div>
                                    <div style="font-size:.75rem;font-weight:600;color:#0A6E54">Term 2 Fees Paid</div>
                                    <div style="font-size:.65rem;color:#0A6E54">Receipt sent to your email</div>
                                </div>
                            </div>
                        </div>
                        <div
                            style="background:var(--white);border-radius:var(--radius-md);padding:var(--space-3);border:1px solid var(--border)">
                            <div style="font-size:.7rem;font-weight:600;color:var(--navy);margin-bottom:8px">📅 Upcoming
                                Events
                            </div>
                            <div style="font-size:.75rem;color:var(--text-muted)">PTA Meeting — 22 Apr</div>
                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px">End of Term Exams — 30
                                Apr
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ─── TESTIMONIALS ──────────────────────────── -->
    <section class="section testimonials-bg" aria-labelledby="testimonials-heading">
        <div class="container">
            <div class="section-header">
                <span class="label">What They Say</span>
                <h2 class="display-2" id="testimonials-heading">Trusted by Educators Across Ghana</h2>
            </div>
            <div class="grid-3">
                <div class="testimonial-card fade-up">
                    <div class="star-row" aria-label="5 star rating">
                        <span class="star" aria-hidden="true">★</span><span class="star" aria-hidden="true">★</span>
                        <span class="star" aria-hidden="true">★</span><span class="star" aria-hidden="true">★</span>
                        <span class="star" aria-hidden="true">★</span>
                    </div>
                    <p class="testimonial-text">"AllAcademies has completely transformed how we manage our school. What
                        used to take hours of paperwork now happens in minutes. The parents love the fee payment
                        portal."</p>
                    <div class="testimonial-author">
                        <div class="t-avatar" style="background:var(--amber-pale);color:#8B5D00">AO</div>
                        <div>
                            <div class="t-name">Ama Owusu</div>
                            <div class="t-role">Headmistress, St. Mary's JHS, Kumasi</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card fade-up stagger-1">
                    <div class="star-row" aria-label="5 star rating">
                        <span class="star" aria-hidden="true">★</span><span class="star" aria-hidden="true">★</span>
                        <span class="star" aria-hidden="true">★</span><span class="star" aria-hidden="true">★</span>
                        <span class="star" aria-hidden="true">★</span>
                    </div>
                    <p class="testimonial-text">"My students' BECE scores improved after using the AI self-assessment.
                        The quizzes identify weak areas automatically — it saves me so much planning time every
                        week."</p>
                    <div class="testimonial-author">
                        <div class="t-avatar" style="background:var(--teal-pale);color:#0A6E54">KM</div>
                        <div>
                            <div class="t-name">Kofi Mensah</div>
                            <div class="t-role">Mathematics Teacher, Accra Academy</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card fade-up stagger-2">
                    <div class="star-row" aria-label="5 star rating">
                        <span class="star" aria-hidden="true">★</span><span class="star" aria-hidden="true">★</span>
                        <span class="star" aria-hidden="true">★</span><span class="star" aria-hidden="true">★</span>
                        <span class="star" aria-hidden="true">★</span>
                    </div>
                    <p class="testimonial-text">"As a parent, I can see my daughter's results, pay fees, and get school
                        notifications all in one app. No more driving to school just to check her reports. Incredible
                        platform."</p>
                    <div class="testimonial-author">
                        <div class="t-avatar" style="background:rgba(11,27,77,.08);color:var(--navy)">EA</div>
                        <div>
                            <div class="t-name">Efua Asare</div>
                            <div class="t-role">Parent, Ridge International School</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FAQ ───────────────────────────────────── -->
    <section class="section faq-bg" aria-labelledby="faq-heading">
        <div class="container">
            <div class="section-header">
                <span class="label">FAQs</span>
                <h2 class="display-2" id="faq-heading">Frequently Asked Questions</h2>
                <p>Can't find what you're looking for? <a href="https://allacademies.com/contact"
                                                          style="color:var(--navy);font-weight:600;text-decoration:underline">Contact
                        our team</a>.</p>
            </div>
            <div class="faq-list" role="list">
                <div class="faq-item" role="listitem">
                    <button class="faq-q" aria-expanded="false">
                        Is AllAcademies free to use?
                        <span class="faq-arrow" aria-hidden="true">▾</span>
                    </button>
                    <div class="faq-a">Yes — AllAcademies offers a free 30-day trial with no credit card required. There
                        are also free resources available to the general public, including government and NGO books.
                        Institutional plans are available at competitive rates on our Pricing page.
                    </div>
                </div>
                <div class="faq-item" role="listitem">
                    <button class="faq-q" aria-expanded="false">
                        How does the school fees payment work?
                        <span class="faq-arrow" aria-hidden="true">▾</span>
                    </button>
                    <div class="faq-a">Parents can visit our School Fees Payment Portal, search for their child's
                        school, and pay securely online. Instant digital receipts are issued and payments are reconciled
                        directly with the school's finance dashboard — no manual processing needed.
                    </div>
                </div>
                <div class="faq-item" role="listitem">
                    <button class="faq-q" aria-expanded="false">
                        Can we use AllAcademies on mobile?
                        <span class="faq-arrow" aria-hidden="true">▾</span>
                    </button>
                    <div class="faq-a">Absolutely. AllAcademies is fully responsive and optimised for mobile, tablet,
                        and desktop. Students, teachers, and parents can access the platform from any device with a
                        browser — no app download required.
                    </div>
                </div>
                <div class="faq-item" role="listitem">
                    <button class="faq-q" aria-expanded="false">
                        How does the AI self-assessment work?
                        <span class="faq-arrow" aria-hidden="true">▾</span>
                    </button>
                    <div class="faq-a">The AI generates personalised practice questions based on the national curriculum
                        (including BECE and WASSCE standards). It analyses each student's responses to identify weak
                        topics, adjusts question difficulty, and tracks improvement over time — giving teachers and
                        students actionable insights.
                    </div>
                </div>
                <div class="faq-item" role="listitem">
                    <button class="faq-q" aria-expanded="false">
                        Can authors publish their books on AllAcademies?
                        <span class="faq-arrow" aria-hidden="true">▾</span>
                    </button>
                    <div class="faq-a">Yes. Ghanaian authors can submit books through our Author Marketplace module.
                        Books can be sold via subscription or one-time purchase. Authors retain revenue tracking and
                        content management tools. Free books can also be submitted for government or NGO distribution.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── CTA SECTION ───────────────────────────── -->
    <section class="section cta-section" aria-labelledby="cta-heading">
        <div class="container">
            <div class="cta-inner">
                <span class="badge badge-navy" style="margin-bottom:var(--space-5);display:inline-block">30-Day Free Trial · No Credit Card</span>
                <h2 class="display-2" id="cta-heading">Ready to Transform Your School?</h2>
                <p class="lead" style="margin-top:var(--space-4)">Join 500+ institutions already running smarter with
                    AllAcademies.<br>Setup takes less than 10 minutes.</p>
                <div class="cta-actions">
                    <a href="https://allacademies.com/register" class="btn btn-primary btn-lg">Get Started Free</a>
                    <a href="https://allacademies.com/contact" class="btn btn-ghost btn-lg">Talk to Sales</a>
                </div>
                <p class="cta-note">Need financial aid? <a href="https://allacademies.com/financial-aid-programs"
                                                           style="color:rgba(255,255,255,.6);text-decoration:underline">Apply
                        here</a></p>
            </div>
        </div>
    </section>

    <!-- ─── NEWSLETTER ─────────────────────────────── -->
    <div class="newsletter section-sm">
        <div class="container">
            <div class="newsletter-inner">
                <h2 class="display-3">Stay in the Loop</h2>
                <p>Get updates on new features, educational resources, and platform news. No spam — ever.</p>
                <form class="newsletter-form" action="https://allacademies.com/newsletter/subscribe" method="POST"
                      novalidate aria-label="Newsletter subscription">
                    <label for="nl-email" class="sr-only">Email address</label>
                    <input type="email" id="nl-email" name="email" class="nl-input" placeholder="Your email address"
                           required autocomplete="email" aria-required="true">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
                <p class="nl-note">5,000+ subscribers · Unsubscribe anytime</p>
            </div>
        </div>
    </div>

</main>

<!-- ─── FOOTER ────────────────────────────────── -->
<footer role="contentinfo">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="logo" style="margin-bottom:var(--space-4)">
                    <div class="logo-icon">AA</div>
                    <div class="logo-text">
                        <span class="logo-name">AllAcademies</span>
                        <span class="logo-tagline" style="color:rgba(255,255,255,.4)">Educational Excellence</span>
                    </div>
                </div>
                <p>Empowering Ghana's education through innovative digital learning solutions.</p>
                <div class="footer-contact">
                    <a href="mailto:allacademies2023@gmail.com" aria-label="Email AllAcademies support">📧
                        allacademies2023@gmail.com</a>
                    <a href="tel:+233556365536" aria-label="Call AllAcademies support">📞 +233 55 636 5536</a>
                    <div style="margin-top:var(--space-3);font-size:.75rem;color:rgba(255,255,255,.35)">Mon–Fri 9am–6pm
                        · Sat–Sun 10am–4pm
                    </div>
                </div>
            </div>

            <div class="footer-col">
                <h4>Platform</h4>
                <a href="https://allacademies.com/features">Features & Modules</a>
                <a href="https://allacademies.com/library">Digital Library</a>
                <a href="https://allacademies.com/pricing">Pricing</a>
                <a href="https://allacademies.com/general/pay/init">Pay School Fees</a>
                <a href="https://allacademies.com/dashboard">Dashboard</a>
            </div>

            <div class="footer-col">
                <h4>Solutions</h4>
                <a href="https://allacademies.com/solutions/schools">For Schools</a>
                <a href="https://allacademies.com/solutions/teachers">For Teachers</a>
                <a href="https://allacademies.com/solutions/students">For Students</a>
                <a href="https://allacademies.com/financial-aid-programs">Financial Aid</a>
            </div>

            <div class="footer-col">
                <h4>Resources</h4>
                <a href="#">Help Center</a>
                <a href="#">Documentation</a>
                <a href="#">Community Forum</a>
                <a href="https://allacademies.com/sponsorship/projects">Sponsorships</a>
                <a href="#">Status Page</a>
            </div>

            <div class="footer-col">
                <h4>Company</h4>
                <a href="https://allacademies.com/contact">Contact Us</a>
                <a href="https://allacademies.com/privacy">Privacy Policy</a>
                <a href="https://allacademies.com/terms">Terms of Service</a>
                <a href="https://allacademies.com/contact">Security</a>
            </div>
        </div>

        <div class="footer-bottom">
            <div>© 2026 AllAcademies. All rights reserved. Transforming education, one innovation at a time.</div>
            <div class="footer-bottom-links">
                <a href="https://allacademies.com/privacy">Privacy</a>
                <a href="https://allacademies.com/terms">Terms</a>
                <a href="https://allacademies.com/contact">Cookies</a>
            </div>
        </div>
    </div>
</footer>

<!-- ─── JAVASCRIPT ─────────────────────────────── -->
<script>
    /* Navbar scroll state */
    const navbar = document.getElementById('navbar');
    const scrolled = () => navbar.classList.toggle('scrolled', window.scrollY > 40);
    window.addEventListener('scroll', scrolled, {passive: true});
    scrolled();

    /* Mobile menu */
    const toggle = document.getElementById('menuToggle');
    const mobileNav = document.getElementById('mobileNav');
    let menuOpen = false;
    toggle.addEventListener('click', () => {
        menuOpen = !menuOpen;
        toggle.classList.toggle('open', menuOpen);
        toggle.setAttribute('aria-expanded', menuOpen);
        mobileNav.classList.toggle('open', menuOpen);
        mobileNav.setAttribute('aria-hidden', !menuOpen);
        if (menuOpen) mobileNav.style.display = 'block';
        else setTimeout(() => {
            mobileNav.style.display = '';
        }, 250);
    });

    /* FAQ accordion */
    document.querySelectorAll('.faq-q').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const open = item.classList.contains('open');
            document.querySelectorAll('.faq-item').forEach(i => {
                i.classList.remove('open');
                i.querySelector('.faq-q').setAttribute('aria-expanded', 'false');
            });
            if (!open) {
                item.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });

    /* User role tabs */
    document.querySelectorAll('.user-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.user-tab').forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            document.querySelectorAll('.user-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            const panelId = 'panel-' + tab.dataset.panel;
            document.getElementById(panelId).classList.add('active');
        });
    });

    /* Scroll animations — Intersection Observer */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                observer.unobserve(e.target);
            }
        });
    }, {threshold: 0.12, rootMargin: '0px 0px -40px 0px'});
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    /* ── Video hero background: trigger CSS scale once loaded ── */
    const bgVideo = document.querySelector('.vh-video');
    if (bgVideo) {
        const onLoad = () => bgVideo.classList.add('loaded');
        bgVideo.addEventListener('canplay', onLoad, {once: true});
        if (bgVideo.readyState >= 3) onLoad();
    }

    /* ── Video hero modal (play demo from hero) ── */
    const vhModal = document.getElementById('vhModal');
    const vhVideo = document.getElementById('vhModalVideo');
    const vhPlayBtn = document.getElementById('vhPlayBtn');
    const vhClose = document.getElementById('vhModalClose');

    function openVhModal() {
        vhModal.classList.add('open');
        document.body.style.overflow = 'hidden';
        vhVideo.play().catch(() => {
        });
        vhClose.focus();
    }

    function closeVhModal() {
        vhModal.classList.remove('open');
        document.body.style.overflow = '';
        vhVideo.pause();
        vhVideo.currentTime = 0;
        vhPlayBtn && vhPlayBtn.focus();
    }

    if (vhPlayBtn) vhPlayBtn.addEventListener('click', openVhModal);
    if (vhClose) vhClose.addEventListener('click', closeVhModal);
    if (vhModal) vhModal.addEventListener('click', e => {
        if (e.target === vhModal) closeVhModal();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && vhModal.classList.contains('open')) closeVhModal();
    });

    /* ── Demo section player (opens same modal) ── */
    const demoPlayer = document.getElementById('demoPlayer');
    if (demoPlayer) {
        const openDemo = () => openVhModal();
        demoPlayer.addEventListener('click', openDemo);
        demoPlayer.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openDemo();
            }
        });
    }
</script>
</body>
</html>

