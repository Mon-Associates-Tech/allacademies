<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        {!! $css !!}

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.55;
            color: #111827;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-top: 1.25em;
            margin-bottom: 0.5em;
            line-height: 1.25;
        }

        p {
            margin: 0 0 0.9em;
        }

        .katex-display {
            margin: 1.2em 0;
            break-inside: avoid;
        }

        .katex {
            font-size: 1.04em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1em 0;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
        }

        pre {
            white-space: pre-wrap;
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
        }

        img {
            max-width: 100%;
        }

        blockquote {
            border-left: 4px solid #d1d5db;
            margin: 1em 0;
            padding-left: 1em;
            color: #374151;
        }

        .page-break {
            break-after: page;
        }
    </style>
</head>
<body>
{!! $body !!}
</body>
</html>
