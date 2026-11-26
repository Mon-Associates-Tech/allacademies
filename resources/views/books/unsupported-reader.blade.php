<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Reader unavailable - {{ config('app.name') }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
            background: #f1f5f9;
            color: #0f172a;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        main {
            width: 100%;
            max-width: 36rem;
        }

        .reader-card {
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 20px 35px rgb(15 23 42 / 8%);
        }

        .reader-card__header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .reader-card__icon {
            display: flex;
            width: 3rem;
            height: 3rem;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            border-radius: 50%;
            background: #fef3c7;
            color: #b45309;
        }

        .reader-card__icon svg {
            width: 1.5rem;
            height: 1.5rem;
        }

        .reader-card__eyebrow {
            margin: 0;
            color: #b45309;
            font-size: .875rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        h1 {
            margin: .25rem 0 0;
            font-size: clamp(1.4rem, 5vw, 1.75rem);
            line-height: 1.25;
            letter-spacing: -.025em;
        }

        .reader-card__content {
            padding: 1.5rem 2rem;
        }

        .reader-card__message {
            margin: 0;
            color: #475569;
            font-size: .95rem;
            line-height: 1.6;
        }

        .reader-card__message strong {
            color: #0f172a;
        }

        .reader-card__actions {
            display: flex;
            justify-content: flex-end;
            gap: .75rem;
            margin-top: 1.5rem;
        }

        .reader-card__button {
            display: inline-flex;
            min-height: 2.75rem;
            align-items: center;
            justify-content: center;
            padding: .65rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: .5rem;
            color: #334155;
            font-size: .9rem;
            font-weight: 650;
            text-align: center;
            text-decoration: none;
            transition: background-color .15s ease, border-color .15s ease;
        }

        .reader-card__button:hover {
            background: #f8fafc;
        }

        .reader-card__button:focus-visible {
            outline: 3px solid #93c5fd;
            outline-offset: 2px;
        }

        .reader-card__button--primary {
            border-color: #2563eb;
            background: #2563eb;
            color: #fff;
        }

        .reader-card__button--primary:hover {
            border-color: #1d4ed8;
            background: #1d4ed8;
        }

        @media (max-width: 40rem) {
            body {
                padding: 1rem;
            }

            .reader-card__header,
            .reader-card__content {
                padding: 1.25rem;
            }

            .reader-card__actions {
                flex-direction: column-reverse;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #020617;
                color: #f1f5f9;
            }

            .reader-card {
                border-color: #1e293b;
                background: #0f172a;
            }

            .reader-card__header {
                border-color: #1e293b;
                background: #020617;
            }

            .reader-card__message {
                color: #cbd5e1;
            }

            .reader-card__message strong,
            h1 {
                color: #f1f5f9;
            }

            .reader-card__button {
                border-color: #475569;
                color: #e2e8f0;
            }

            .reader-card__button:hover {
                background: #1e293b;
            }
        }
    </style>
</head>
<body>
    <main>
        <section class="reader-card" aria-labelledby="fallback-title">
            <div class="reader-card__header">
                <div class="reader-card__icon" aria-hidden="true">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m9-0.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 4.5h.008v.008H12V16.5Z" />
                    </svg>
                </div>
                <p class="reader-card__eyebrow">Reader unavailable</p>
                <h1 id="fallback-title">This format cannot be read in the browser</h1>
            </div>

            <div class="reader-card__content">
                <p class="reader-card__message">
                    The <strong>{{ strtoupper($format) }}</strong> book format is not supported by the online reader. You can return to the book page or download the file if your account allows it.
                </p>
                <div class="reader-card__actions">
                    <a href="{{ route('books.show', $book) }}" class="reader-card__button">
                        Back to book
                    </a>
                    @if($signedDownloadUrl)
                        <a href="{{ $signedDownloadUrl }}" class="reader-card__button reader-card__button--primary">
                            Download book
                        </a>
                    @endif
                </div>
            </div>
        </section>
    </main>
</body>
</html>