<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Share {{ ucfirst($action) }}</title>
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
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
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
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
        }
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        .status-declined {
            background-color: #f8d7da;
            color: #721c24;
        }
        .book-info {
            background-color: #f8f9fa;
            border-left: 4px solid #ff7e5f;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .user-info {
            display: flex;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #e9f7fe;
            border-radius: 5px;
        }
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #ddd;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
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
            <h1>📖 Book Share {{ ucfirst($action) }}</h1>
        </div>

        <div class="content">
            <p>Hello {{ $share->sharedBy->name }},</p>

            <div class="user-info">
                <div class="avatar">{{ substr($recipient->name, 0, 1) }}</div>
                <div>
                    <strong>{{ $recipient->name }}</strong> has
                    <span class="status-badge status-{{ $action }}">{{ $action }}</span>
                    your book share
                </div>
            </div>

            <div class="book-info">
                <h3>{{ $book->title }}</h3>
                @if($book->description)
                    <p>{{ Str::limit($book->description, 150) }}</p>
                @endif
                @if($book->cover_image)
                    <img src="{{ $book->cover_image_url }}" alt="Book Cover" style="max-width: 150px; height: auto; border-radius: 5px;">
                @endif
            </div>

            @if($action === 'accepted')
                <p>🎉 Great news! They can now access and read your shared book.</p>
            @else
                <p>They will not be able to access the book content.</p>
            @endif

            <div class="signature">
                <p>Thank you for sharing on {{ config('app.name') }}!<br>{{ config('app.name') }} Team</p>
            </div>
        </div>

        <div class="footer">
            <p>This notification was sent because someone responded to your book share.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
