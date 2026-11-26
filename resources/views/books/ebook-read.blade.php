@php($formatLabel = strtoupper($bookFormat))
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $book->title }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/ebook_reader.js'])
    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #f1f5f9;
            color: #0f172a;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        header {
            display: flex;
            min-height: 4rem;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .5rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            background: #fff;
        }

        header > div:first-child,
        header > div:last-child {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .5rem;
        }

        header > div:last-child {
            flex-shrink: 0;
        }

        header h1,
        header p {
            margin: 0;
        }

        header h1 {
            overflow: hidden;
            font-size: .875rem;
            font-weight: 600;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        header p {
            color: #64748b;
            font-size: .75rem;
        }

        header a,
        header button {
            display: inline-flex;
            min-height: 2rem;
            align-items: center;
            justify-content: center;
            padding: .375rem .75rem;
            border: 1px solid #cbd5e1;
            border-radius: .375rem;
            background: #fff;
            color: #334155;
            font: inherit;
            font-size: .875rem;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
        }

        header a:hover,
        header button:hover {
            background: #f1f5f9;
        }

        #reader-download,
        #reader-error .reader-action--primary {
            border-color: #2563eb;
            background: #2563eb;
            color: #fff;
        }

        main {
            min-height: 0;
            flex: 1;
            padding: .5rem;
        }

        #book-ebook-reader {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
            background: #fff;
            box-shadow: 0 1px 3px rgb(15 23 42 / 10%);
        }

        #reader-content {
            width: 100%;
            height: 100%;
        }

        #reader-status,
        #reader-error {
            position: absolute;
            inset: 0;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
        }

        #reader-status {
            z-index: 10;
            display: flex;
            background: rgb(255 255 255 / 92%);
            color: #475569;
            font-size: .875rem;
        }

        #reader-error {
            z-index: 20;
            display: flex;
            background: #fff;
        }

        #reader-error[hidden],
        #reader-status[hidden] {
            display: none !important;
        }

        #reader-error > div {
            max-width: 32rem;
        }

        #reader-error h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
        }

        #reader-error p {
            margin: .5rem 0 0;
            color: #475569;
            font-size: .875rem;
            line-height: 1.5;
        }

        #reader-error a {
            display: inline-flex;
            margin: 1rem .25rem 0;
            padding: .5rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: .375rem;
            color: #334155;
            font-size: .875rem;
            text-decoration: none;
        }

        #reader-error .reader-action--primary {
            border-color: #2563eb;
            background: #2563eb;
            color: #fff;
        }

        @media (max-width: 40rem) {
            header {
                min-height: auto;
                align-items: flex-start;
                flex-direction: column;
            }

            header > div:last-child {
                width: 100%;
                flex-wrap: wrap;
            }

            main {
                padding: .5rem;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #020617;
                color: #f1f5f9;
            }

            header,
            header a,
            header button,
            #book-ebook-reader,
            #reader-error {
                border-color: #334155;
                background: #0f172a;
                color: #e2e8f0;
            }

            header a:hover,
            header button:hover {
                background: #1e293b;
            }

            #reader-download,
            #reader-error .reader-action--primary {
                border-color: #2563eb;
                background: #2563eb;
                color: #fff;
            }

            #reader-status {
                background: rgb(15 23 42 / 94%);
                color: #cbd5e1;
            }

            #reader-error p,
            header p {
                color: #cbd5e1;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <header class="flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex min-w-0 items-center gap-3">
            <a href="{{ route('books.show', $book) }}" class="shrink-0 rounded border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Back</a>
            <div class="min-w-0">
                <h1 class="truncate text-sm font-semibold" title="{{ $book->title }}">{{ $book->title }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $formatLabel }} reader</p>
            </div>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <button id="reader-previous" type="button" class="rounded border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" aria-label="Previous page">Previous</button>
            <span id="reader-progress-label" class="min-w-16 text-center text-xs text-slate-600 dark:text-slate-300" aria-live="polite">Opening</span>
            <button id="reader-next" type="button" class="rounded border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800" aria-label="Next page">Next</button>
            @if($signedDownloadUrl)
                <a id="reader-download" href="{{ $signedDownloadUrl }}" class="rounded bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">Download</a>
            @endif
        </div>
    </header>

    <main class="h-[calc(100vh-4rem)] p-2 sm:p-4">
        <div
            id="book-ebook-reader"
            class="relative h-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
            data-stream-url="{{ $streamUrl }}"
            data-progress-url="{{ $progressUrl }}"
            data-book-id="{{ $book->id }}"
            data-total-pages="{{ max((int) ($book->pages ?? 0), 0) }}"
            data-initial-progress="{{ $initialProgress }}"
        >
            <div id="reader-content" class="h-full w-full"></div>
            <div id="reader-status" class="absolute inset-0 z-10 flex items-center justify-center bg-white/90 p-6 text-center text-sm text-slate-600 dark:bg-slate-900/90 dark:text-slate-300" role="status">
                Preparing your book…
            </div>
            <div id="reader-error" class="absolute inset-0 z-20 items-center justify-center bg-white p-6 text-center dark:bg-slate-900" role="alert" aria-live="assertive" hidden>
                <div class="max-w-lg">
                    <h2 class="text-lg font-semibold">This book could not be opened</h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">The file may be damaged or protected with DRM. Try downloading it, or ask the library for another copy.</p>
                    @if($signedDownloadUrl)
                        <a href="{{ $signedDownloadUrl }}" class="reader-action--primary mt-4 inline-flex rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Download book</a>
                    @endif
                    <a href="{{ route('books.show', $book) }}" class="reader-action mt-4 inline-flex rounded border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Back to book</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>