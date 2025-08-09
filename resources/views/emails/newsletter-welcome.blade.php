<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome to {{ config('app.name') }} Newsletter</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #2d3748; background-color: #f7fafc; margin: 0; padding: 0; width: 100% !important;">

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f7fafc; padding: 20px 0;">
    <tr>
        <td align="center">
            <!-- Main Container -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">

                <!-- Header -->
                <tr>
                    <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; padding: 40px 30px; text-align: center;">
                        <!-- Logo -->
                        <div style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; backdrop-filter: blur(10px);">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" style="color: white;">
                                <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
                            </svg>
                        </div>

                        <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 8px 0; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); color: white;">
                            Welcome to {{ config('app.name') }}!
                        </h1>
                        <p style="font-size: 16px; margin: 0; opacity: 0.9; color: white;">
                            Thank you for joining our educational community
                        </p>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding: 40px 30px;">
                        <h2 style="font-size: 20px; font-weight: 600; color: #2d3748; margin: 0 0 20px 0;">
                            Hi{{ $subscription->name ? ' ' . $subscription->name : '' }}! 👋
                        </h2>

                        <p style="font-size: 16px; color: #4a5568; margin-bottom: 30px; line-height: 1.7;">
                            We're thrilled to have you join our growing community of learners, educators, and knowledge seekers!
                            You've just taken an important step toward staying connected with the latest in educational innovation.
                        </p>

                        <!-- Features Section -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #f8fafc; border-radius: 8px; padding: 24px; margin: 30px 0; border-left: 4px solid #667eea;">
                            <tr>
                                <td>
                                    <h3 style="font-size: 18px; font-weight: 600; color: #2d3748; margin: 0 0 16px 0;">
                                        Here's what you can expect from us:
                                    </h3>

                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 8px 0; vertical-align: top;">
                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td style="vertical-align: top; padding-right: 12px; padding-top: 2px;">
                                                            <div style="width: 20px; height: 20px; background: #48bb78; border-radius: 10px; text-align: center; line-height: 20px; display: inline-block;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                                                                    <polyline points="20,6 9,17 4,12"></polyline>
                                                                </svg>
                                                            </div>
                                                        </td>


                                                        <td style="font-size: 15px; color: #4a5568; line-height: 1.5;">
                                                            <strong>Weekly Educational Insights:</strong> Curated content, teaching strategies, and learning resources
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; vertical-align: top;">
                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td style="vertical-align: top; padding-right: 12px; padding-top: 2px;">
                                                            <div style="width: 20px; height: 20px; background: #48bb78; border-radius: 10px; text-align: center; line-height: 20px; display: inline-block;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                                                                    <polyline points="20,6 9,17 4,12"></polyline>
                                                                </svg>
                                                            </div>
                                                        </td>

                                                        <td style="font-size: 15px; color: #4a5568; line-height: 1.5;">
                                                            <strong>Product Updates:</strong> Be the first to know about new features and improvements
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; vertical-align: top;">
                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td style="vertical-align: top; padding-right: 12px; padding-top: 2px;">
                                                            <div style="width: 20px; height: 20px; background: #48bb78; border-radius: 10px; text-align: center; line-height: 20px; display: inline-block;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                                                                    <polyline points="20,6 9,17 4,12"></polyline>
                                                                </svg>
                                                            </div>
                                                        </td>

                                                        <td style="font-size: 15px; color: #4a5568; line-height: 1.5;">
                                                            <strong>Exclusive Content:</strong> Early access to webinars, courses, and educational materials
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; vertical-align: top;">
                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td style="vertical-align: top; padding-right: 12px; padding-top: 2px;">
                                                            <div style="width: 20px; height: 20px; background: #48bb78; border-radius: 10px; text-align: center; line-height: 20px; display: inline-block;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                                                                    <polyline points="20,6 9,17 4,12"></polyline>
                                                                </svg>
                                                            </div>
                                                        </td>

                                                        <td style="font-size: 15px; color: #4a5568; line-height: 1.5;">
                                                            <strong>Special Offers:</strong> Subscriber-only discounts and promotions
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; vertical-align: top;">
                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                                    <tr>
                                                        <td style="vertical-align: top; padding-right: 12px; padding-top: 2px;">
                                                            <div style="width: 20px; height: 20px; background: #48bb78; border-radius: 10px; text-align: center; line-height: 20px; display: inline-block;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                                                                    <polyline points="20,6 9,17 4,12"></polyline>
                                                                </svg>
                                                            </div>
                                                        </td>

                                                        <td style="font-size: 15px; color: #4a5568; line-height: 1.5;">
                                                            <strong>Community Highlights:</strong> Success stories and tips from fellow educators
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Promise Box -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: #edf2f7; border-radius: 8px; padding: 20px; margin: 25px 0; border-left: 4px solid #48bb78;">
                            <tr>
                                <td>
                                    <p style="font-size: 15px; color: #2d3748; margin: 0; font-weight: 500;">
                                        <strong>Our Promise:</strong> We respect your time and inbox. You'll only receive valuable,
                                        actionable content that helps you in your educational journey. No spam, ever.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Stats Section -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; margin: 30px 0; border-radius: 8px; text-align: center;">
                            <tr>
                                <td>
                                    <h3 style="margin: 0 0 5px 0; font-size: 18px; color: white;">Join Our Growing Community</h3>
                                    <p style="margin: 0 0 20px 0; opacity: 0.9; font-size: 14px; color: white;">Trusted by educators worldwide</p>

                                    <!-- Stats Grid -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="text-align: center; padding: 0 15px;">
                                                <div style="font-size: 24px; font-weight: 700; color: white;">10,000+</div>
                                                <div style="font-size: 12px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; color: white;">Active Users</div>
                                            </td>
                                            <td style="text-align: center; padding: 0 15px;">
                                                <div style="font-size: 24px; font-weight: 700; color: white;">500+</div>
                                                <div style="font-size: 12px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; color: white;">Institutions</div>
                                            </td>
                                            <td style="text-align: center; padding: 0 15px;">
                                                <div style="font-size: 24px; font-weight: 700; color: white;">98%</div>
                                                <div style="font-size: 12px; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; color: white;">Satisfaction Rate</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Call to Action -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align: center; margin: 40px 0;">
                            <tr>
                                <td>
                                    <a href="{{ route('home') }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                        Explore {{ config('app.name') }} Now →
                                    </a>
                                    <p style="font-size: 14px; color: #718096; margin-top: 15px;">
                                        Ready to transform your educational experience?
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                        <!-- Social Links -->
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0;">
                            <tr>
                                <td align="center">
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; margin: 0 8px; text-decoration: none; line-height: 40px; color: #4a5568; text-align: center; font-weight: bold;">f</a>
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; margin: 0 8px; text-decoration: none; line-height: 40px; color: #4a5568; text-align: center; font-weight: bold;">t</a>
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; margin: 0 8px; text-decoration: none; line-height: 40px; color: #4a5568; text-align: center; font-weight: bold;">in</a>
                                    <a href="#" style="display: inline-block; width: 40px; height: 40px; background: #e2e8f0; border-radius: 50%; margin: 0 8px; text-decoration: none; line-height: 40px; color: #4a5568; text-align: center; font-weight: bold;">ig</a>
                                </td>
                            </tr>
                        </table>

                        <!-- Footer Links -->
                        <div style="font-size: 14px; color: #718096; margin: 20px 0 10px 0;">
                            <a href="{{ route('home') }}" style="color: #667eea; text-decoration: none; margin: 0 8px;">Website</a>
                            <a href="{{ route('branding.contact') }}" style="color: #667eea; text-decoration: none; margin: 0 8px;">Contact</a>
                            <a href="{{ route('branding.privacy') }}" style="color: #667eea; text-decoration: none; margin: 0 8px;">Privacy</a>
                            <a href="{{ $subscription->getUnsubscribeUrl() }}" style="color: #667eea; text-decoration: none; margin: 0 8px;">Unsubscribe</a>
                        </div>

                        <!-- Copyright -->
                        <p style="font-size: 12px; color: #a0aec0; margin: 0;">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                            <span style="font-size: 11px;">
                                    You received this email because you subscribed to our newsletter.
                                    You can <a href="{{ $subscription->getUnsubscribeUrl() }}" style="color: #667eea;">unsubscribe</a> at any time.
                                </span>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
