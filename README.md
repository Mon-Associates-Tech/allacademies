# All Academies

A comprehensive multi-tenant school management and educational platform built with Laravel 12, providing AI-powered learning tools, digital library management, and complete academic operations.

## About All Academies

All Academies is a SaaS platform designed to streamline educational institution management while enhancing the learning experience through AI integration. The platform supports multiple schools with isolated data, role-based access control, and extensive academic features.

## Key Features

### Academic Management
- Multi-level academic structure (Groups, Levels, Subjects, Topics, Subtopics)
- Academic year and period management
- Student progression tracking and report cards
- Attendance management
- Grade scales and performance analytics

### AI-Powered Learning
- Academic chat with OpenAI integration (GPT-3.5, GPT-4)
- Token-based subscription system for AI usage
- Automated quiz and examination generation
- Personalized learning recommendations
- Content generation from book chapters

### Digital Library
- Book catalog with categories and metadata
- Book borrowing and inventory management
- Digital book subscriptions (individual and group)
- Reading progress tracking with achievements
- Audio book conversion support
- Book sharing between users and groups

### Assessment System
- Multiple question types (MCQ, Essay, True/False)
- Quiz and examination creation
- Assignment management with submissions
- Automated grading for objective questions
- Essay assessment workflow
- Performance analytics and insights

### Virtual Classroom
- BigBlueButton integration for live sessions
- Recurring session scheduling
- Session recordings and playback
- Participant management
- Automated reminders

### Communication & Collaboration
- Internal messaging system
- Forum with categories and moderation
- Note-taking with sharing capabilities
- Real-time notifications
- Activity tracking

### Financial Management
- School fee structures and payment tracking
- Financial aid management
- Paystack payment integration
- Subaccount management for schools
- Donation tracking

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2
- **Frontend**: Livewire 3, Alpine.js 3, Tailwind CSS 3
- **Database**: MySQL/PostgreSQL
- **AI Integration**: OpenAI PHP Laravel
- **Payment**: Paystack
- **Video Conferencing**: BigBlueButton
- **Storage**: AWS S3, Local
- **Queue**: Redis/Database
- **Testing**: PHPUnit

## Installation

```bash
# Clone repository
git clone <repository-url>
cd allacademies

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Storage link
php artisan storage:link

# Build assets
npm run build

# Start development server
php artisan serve
```

## Configuration

Configure the following in your `.env` file:

```env
# OpenAI Integration
OPENAI_API_KEY=your_openai_key

# Paystack Payment
PAYSTACK_PUBLIC_KEY=your_public_key
PAYSTACK_SECRET_KEY=your_secret_key

# AWS S3 Storage
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket

# BigBlueButton
BBB_SERVER_BASE_URL=your_bbb_url
BBB_SECRET=your_bbb_secret
```

## Queue Workers

Start queue workers for background processing:

```bash
php artisan queue:work
```

## Scheduled Tasks

Add to crontab for scheduled tasks:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## User Roles

- **Super Admin**: Platform-wide management
- **Owner**: School owner with full access
- **Admin**: School administrator
- **Teacher**: Create lessons, assignments, grade students
- **Student**: Access learning materials, complete assignments
- **Librarian**: Manage book inventory and subscriptions
- **Author**: Publish and manage books
- **Parent**: Monitor student progress
- **Accountant**: Financial tracking and reporting
- **Guest**: Limited access user

## Multi-Tenancy

The platform uses school-based data isolation:
- Each school has isolated data
- Global scopes ensure data separation
- Cross-school access for super admins
- School context management

## Code Quality

```bash
# Format code
php artisan pint

# Run tests
php artisan test

# Static analysis
vendor/bin/phpstan analyse
```

## License

Proprietary software. All rights reserved.
