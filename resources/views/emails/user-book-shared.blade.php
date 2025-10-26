<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Shared - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .intro-text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 30px;
        }
        .book-card {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .book-card-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .book-cover {
            width: 80px;
            height: 110px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .book-info h2 {
            margin: 0 0 10px 0;
            font-size: 20px;
            color: #2d3748;
            font-weight: 600;
        }
        .book-meta {
            font-size: 14px;
            color: #718096;
            margin: 5px 0;
        }
        .book-meta strong {
            color: #4a5568;
            font-weight: 600;
        }
        .book-description {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
        }
        .note-box {
            background-color: #edf2f7;
            border-left: 4px solid #4299e1;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .note-box-title {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }
        .note-box-content {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.5;
        }
        .warning-box {
            background-color: #fff5f5;
            border-left: 4px solid #fc8181;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .warning-box-content {
            font-size: 14px;
            color: #742a2a;
            line-height: 1.5;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button-primary {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
            transition: transform 0.2s;
        }
        .button-primary:hover {
            transform: translateY(-2px);
        }
        .button-secondary {
            display: inline-block;
            background-color: #edf2f7;
            color: #4a5568 !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            margin-top: 10px;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
        .footer-text {
            font-size: 14px;
            color: #718096;
            text-align: center;
            margin-top: 20px;
        }
        .email-footer {
            background-color: #f7fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            .book-card-header {
                flex-direction: column;
            }
            .book-cover {
                margin-bottom: 15px;
            }
            .button-primary {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <!-- Header -->
    <div class="email-header">
        <h1>📚 New Book Shared With You!</h1>
    </div>

    <!-- Body -->
    <div class="email-body">
        <div class="greeting">
            Hello {{ $notifiable->name }},
        </div>

        <p class="intro-text">
            <strong>{{ $sharedBy->name }}</strong> has shared a book with you on {{ config('app.name') }}.
        </p>

        <!-- Book Card -->
        <div class="book-card">
            <div class="book-card-header">
                @if($userBook->cover_image)
                    <img src="{{ asset('storage/' . $userBook->cover_image) }}"
                         alt="{{ $userBook->title }}"
                         class="book-cover">
                @else
                    <img src="{{ asset('images/book-cover.png') }}"
                         alt="Book Cover"
                         class="book-cover">
                @endif

                <div class="book-info">
                    <h2>{{ $userBook->title }}</h2>
                    <div class="book-meta">
                        <strong>Shared via:</strong> {{ $share->getShareTargetName() }}
                    </div>
                    @if($userBook->pages)
                        <div class="book-meta">
                            <strong>Pages:</strong> {{ $userBook->pages }}
                        </div>
                    @endif
                    @if($userBook->edition)
                        <div class="book-meta">
                            <strong>Edition:</strong> {{ $userBook->edition }}
                        </div>
                    @endif
                </div>
            </div>

            @if($userBook->description)
                <div class="book-description">
                    {{ $userBook->description }}
                </div>
            @endif
        </div>

        <!-- Personal Note -->
        @if($share->notes)
            <div class="note-box">
                <div class="note-box-title">
                    💬 Message from {{ $sharedBy->name }}:
                </div>
                <div class="note-box-content">
                    {{ $share->notes }}
                </div>
            </div>
        @endif

        <!-- Expiration Warning -->
        @if($share->expires_at)
            <div class="warning-box">
                <div class="warning-box-content">
                    ⏰ <strong>Please note:</strong> This access will expire on
                    <strong>{{ $share->expires_at->format('F j, Y') }}</strong>
                </div>
            </div>
        @endif

        <!-- Primary Action Button -->
        <div class="button-container">
            <a href="{{ route('user-books.show', $userBook) }}" class="button-primary">
                📖 View Book Now
            </a>
        </div>

        <div class="divider"></div>

        <!-- Secondary Action -->
        <div class="button-container">
            <a href="{{ route('user-books.shared') }}" class="button-secondary">
                View All Shared Books
            </a>
        </div>

        <p class="footer-text">
            You can access this book anytime from your shared books section.
        </p>
    </div>

    <!-- Footer -->
    <div class="email-footer">
        <p>
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
        <p>
            You received this email because a book was shared with you on our platform.
        </p>
        @if(config('app.url'))
            <p>
                <a href="{{ config('app.url') }}">Visit {{ config('app.name') }}</a>
            </p>
        @endif
    </div>
</div>
</body>
</html>
