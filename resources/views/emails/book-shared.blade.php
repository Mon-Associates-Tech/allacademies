<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Shared with You</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .content p {
            font-size: 16px;
            margin: 15px 0;
        }
        .book-info {
            background-color: #f8f9fa;
            border-left: 4px solid #2575fc;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 117, 252, 0.3);
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 117, 252, 0.4);
        }
        .footer {
            background-color: #f1f3f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .signature {
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 New Book Shared!</h1>
        </div>

        <div class="content">
            <p>Hello {{ $share->sharedTo->name }},</p>

            <p>Great news! <strong>{{ $sharer->name }}</strong> has shared a book with you.</p>

            <div class="book-info">
                <h3>{{ $book->title }}</h3>
                @if($book->description)
                    <p>{{ Str::limit($book->description, 150) }}</p>
                @endif
                @if($book->cover_image)
                    <img src="{{ $book->cover_image_url }}" alt="Book Cover" style="max-width: 150px; height: auto; border-radius: 5px;">
                @endif
            </div>

            <p>You now have access to this book in your shared library. Start reading right away!</p>

            <div class="button-container">
                <a href="{{ route('user-books.shared') }}" class="cta-button">
                    View Shared Books
                </a>
            </div>

            <div class="signature">
                <p>Happy reading!<br>{{ config('app.name') }} Team</p>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent to you because you were shared a book on {{ config('app.name') }}.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
