
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Assignment</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9fafb; padding: 20px; }
        .footer { background-color: #e5e7eb; padding: 15px; text-align: center; font-size: 12px; }
        .assignment-details { background-color: white; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .btn { background-color: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Assignment Assigned</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $student->user->name ?? 'Student' }},</p>
            
            <p>{{ $teacherName }} has assigned you a new {{ $assignment->type }}.</p>
            
            <div class="assignment-details">
                <h3>{{ $assignment->title }}</h3>
                
                <p><strong>Subject:</strong> {{ $subjectName }}</p>
                <p><strong>Type:</strong> {{ ucfirst($assignment->type) }}</p>
                <p><strong>Duration:</strong> {{ $assignment->duration_in_minutes }} minutes</p>
                <p><strong>Total Marks:</strong> {{ $assignment->total_marks }}</p>
                <p><strong>Starts:</strong> {{ $assignment->starts_at?->format('F j, Y \a\t g:i A') }}</p>
                <p><strong>Ends:</strong> {{ $assignment->ends_at?->format('F j, Y \a\t g:i A') }}</p>
                
                @if($assignment->description)
                    <p><strong>Description:</strong> {{ $assignment->description }}</p>
                @endif
                
                @if($assignment->instructions)
                    <p><strong>Instructions:</strong> {{ $assignment->instructions }}</p>
                @endif
            </div>
            
            <p>Please log in to your account to view and complete this assignment.</p>
            
            <p style="text-align: center;">
                <a href="{{ config('app.url') }}" class="btn">Go to Dashboard</a>
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
