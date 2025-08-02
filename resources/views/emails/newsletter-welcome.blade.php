
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name') }} Newsletter</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f7fafc; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 32px 24px; text-align: center; }
        .content { padding: 32px 24px; }
        .footer { background: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0; }
        .button { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p>Thank you for subscribing to our newsletter</p>
        </div>
        
        <div class="content">
            <h2>Hi{{ $subscription->name ? ' ' . $subscription->name : '' }}!</h2>
            
            <p>We're excited to have you join our community of learners and educators!</p>
            
            <p>You'll receive:</p>
            <ul>
                <li>Latest educational content and resources</li>
                <li>Product updates and new features</li>
                <li>Tips and best practices for online learning</li>
                <li>Exclusive offers and early access to new content</li>
            </ul>
            
            <p>We respect your inbox and promise to only send you valuable content.</p>
            
            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ route('home') }}" class="button">Explore {{ config('app.name') }}</a>
            </div>
        </div>
        
        <div class="footer">
            <p>You can <a href="{{ $subscription->getUnsubscribeUrl() }}">unsubscribe</a> at any time.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
