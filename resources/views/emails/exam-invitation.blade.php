<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea; border-radius: 4px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .info-label { font-weight: bold; color: #6b7280; }
        .info-value { color: #111827; }
        .access-code { background: #fef3c7; border: 2px dashed #f59e0b; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 2px; margin: 20px 0; border-radius: 8px; }
        .button { display: inline-block; background: #667eea; color: white; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .button:hover { background: #5568d3; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
        .alert { background: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">{{ $isReminder ? '⏰ Exam Reminder' : '📧 Exam Invitation' }}</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $participantName }},</p>
            
            @if($isReminder)
                <p>This is a reminder that you have an upcoming examination:</p>
            @else
                <p>You have been invited to participate in the following examination:</p>
            @endif
            
            <div class="info-box">
                <h2 style="margin-top: 0; color: #667eea;">{{ $exam->title }}</h2>
                
                @if($exam->description)
                    <p style="color: #6b7280;">{{ $exam->description }}</p>
                @endif
                
                <div style="margin-top: 20px;">
                    <div class="info-row">
                        <span class="info-label">Start Date & Time:</span>
                        <span class="info-value">{{ $exam->start_datetime ? \Carbon\Carbon::parse($exam->start_datetime)->format('F j, Y \a\t g:i A') : 'Not set' }}</span>
                    </div>
                    
                    @if($exam->end_datetime)
                        <div class="info-row">
                            <span class="info-label">End Date & Time:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($exam->end_datetime)->format('F j, Y \a\t g:i A') }}</span>
                        </div>
                    @endif
                    
                    @if($exam->duration_in_minutes)
                        <div class="info-row">
                            <span class="info-label">Duration:</span>
                            <span class="info-value">{{ $exam->duration_in_minutes }} minutes</span>
                        </div>
                    @endif
                    
                    <div class="info-row" style="border-bottom: none;">
                        <span class="info-label">Total Sections:</span>
                        <span class="info-value">{{ $exam->sections->count() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="access-code">
                <div style="font-size: 14px; color: #6b7280; margin-bottom: 5px;">Access Code</div>
                {{ $exam->access_code }}
            </div>
            
            @if($uniqueCode)
                <div style="background: #dbeafe; border: 2px solid #3b82f6; padding: 15px; text-align: center; margin: 20px 0; border-radius: 8px;">
                    <div style="font-size: 14px; color: #1e40af; margin-bottom: 5px;">Your Unique Code</div>
                    <div style="font-size: 20px; font-weight: bold; color: #1e3a8a; letter-spacing: 1px;">{{ $uniqueCode }}</div>
                </div>
            @endif
            
            @if($exam->instructions)
                <div class="alert">
                    <strong>⚠️ Important Instructions:</strong>
                    <p style="margin: 10px 0 0 0;">{{ $exam->instructions }}</p>
                </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ $joinUrl }}?code={{ $exam->access_code }}" class="button">Join Examination</a>
            </div>
            
            @if($exam->start_datetime)
                <p style="margin-top: 30px; font-size: 14px; color: #6b7280;">
                    <strong>📅 Calendar Reminder:</strong> A calendar file (.ics) is attached to this email. 
                    Click on it to add this exam to your calendar application (Google Calendar, Outlook, Apple Calendar, etc.).
                </p>
                
                <p style="font-size: 14px; color: #6b7280;">
                    <strong>⏰ Time Until Exam:</strong> 
                    {{ \Carbon\Carbon::parse($exam->start_datetime)->diffForHumans() }}
                </p>
            @endif
        </div>
        
        <div class="footer">
            <p>This is an automated message from All Academies Examination System.</p>
            <p>If you have any questions, please contact your exam administrator.</p>
        </div>
    </div>
</body>
</html>
