<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Shared with You</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            font-size: 16px;
            margin: 15px 0;
            color: #4a5568;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .note-info {
            background: linear-gradient(135deg, #f6f8fb 0%, #eef2f7 100%);
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .note-info h2 {
            margin: 0 0 12px 0;
            font-size: 20px;
            color: #2d3748;
            font-weight: 700;
        }
        .note-metadata {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .metadata-item {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #718096;
        }
        .metadata-item .icon {
            margin-right: 6px;
            font-size: 16px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        .badge-edit {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .badge-view {
            background-color: #bee3f8;
            color: #2c5282;
        }
        .note-preview {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            max-height: 200px;
            overflow: hidden;
            position: relative;
        }
        .note-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(to bottom, transparent, #ffffff);
        }
        .note-preview p {
            margin: 8px 0;
            font-size: 14px;
            color: #4a5568;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .sharer-info {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f7fafc;
            border-radius: 8px;
            margin: 20px 0;
        }
        .sharer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-right: 15px;
        }
        .sharer-details h3 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: #2d3748;
        }
        .sharer-details p {
            margin: 0;
            font-size: 14px;
            color: #718096;
        }
        .footer {
            background-color: #f1f3f9;
            padding: 25px 20px;
            text-align: center;
            font-size: 14px;
            color: #718096;
        }
        .footer p {
            margin: 8px 0;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-style: italic;
            color: #718096;
        }
        .tips {
            background-color: #ebf8ff;
            border-left: 4px solid #4299e1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .tips h4 {
            margin: 0 0 10px 0;
            color: #2c5282;
            font-size: 14px;
            font-weight: 600;
        }
        .tips ul {
            margin: 0;
            padding-left: 20px;
        }
        .tips li {
            font-size: 13px;
            color: #2d3748;
            margin: 5px 0;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 5px;
            }
            .content {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
            .cta-button {
                padding: 14px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="icon">📝</div>
        <h1>Note Shared with You!</h1>
    </div>

    <div class="content">
        <p class="greeting">Hello {{ $recipient->name }},</p>

        <div class="sharer-info">
            <div class="sharer-avatar">
                {{ strtoupper(substr($sharer->name, 0, 1)) }}
            </div>
            <div class="sharer-details">
                <h3>{{ $sharer->name }}</h3>
                <p>has shared a note with you</p>
            </div>
        </div>

        <p>You now have {{ $canEdit ? 'edit' : 'view' }} access to this note. This is a great resource that {{ $sharer->name }} thought would be helpful for your studies!</p>

        <div class="note-info">
            <h2>{{ $note->title }}</h2>

            <span class="badge {{ $canEdit ? 'badge-edit' : 'badge-view' }}">
                    {{ $canEdit ? '✏️ Can Edit' : '👁️ View Only' }}
                </span>

            <div class="note-metadata">
                @if($note->academicSubject)
                    <div class="metadata-item">
                        <span class="icon">📚</span>
                        <span>{{ $note->academicSubject->name }}</span>
                    </div>
                @endif

                @if($note->book)
                    <div class="metadata-item">
                        <span class="icon">📖</span>
                        <span>{{ $note->book->title }}</span>
                    </div>
                @endif

                <div class="metadata-item">
                    <span class="icon">📅</span>
                    <span>{{ $note->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        @if($note->content)
            <div class="note-preview">
                {!! Str::limit(strip_tags($note->content), 300) !!}
            </div>
        @endif

        <div class="button-container">
            <a href="{{ $noteUrl }}" class="cta-button">
                📝 View Note Now
            </a>
        </div>

        @if($canEdit)
            <div class="tips">
                <h4>💡 What you can do:</h4>
                <ul>
                    <li>Read and study the note content</li>
                    <li>Edit and improve the note</li>
                    <li>Add your own insights and observations</li>
                    <li>Collaborate with {{ $sharer->name }} to enhance the content</li>
                </ul>
            </div>
        @endif

        <div class="signature">
            <p>Make the most of this shared knowledge!<br>
                <strong>{{ config('app.name') }} Team</strong></p>
        </div>
    </div>

    <div class="footer">
        <p>This email was sent to you because {{ $sharer->name }} shared a note with you on {{ config('app.name') }}.</p>
        <p>If you have any questions, feel free to reach out to your school administrator.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
