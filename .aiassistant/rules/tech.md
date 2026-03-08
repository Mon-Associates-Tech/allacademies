---
apply: always
---

# Technology Stack

## Programming Languages
- **PHP**: ^8.1 (Primary backend language)
- **JavaScript**: ES6+ (Frontend interactivity)
- **SQL**: Database queries and migrations
- **Blade**: Laravel templating engine
- **CSS**: Tailwind CSS framework

## Backend Framework & Core
- **Laravel**: ^12 (PHP web application framework)
- **Livewire**: ^3.6 (Full-stack framework for dynamic interfaces)
- **Laravel Sanctum**: API authentication
- **Laravel Telescope**: ^5.7 (Debugging and monitoring)
- **Laravel Tinker**: ^2.7 (REPL for Laravel)

## Database
- **MySQL/PostgreSQL**: Primary database (configured via Laravel)
- **Doctrine DBAL**: ^3.7 (Database abstraction layer)
- **Redis**: Caching and queue management (optional)

## Frontend Technologies

### CSS Framework
- **Tailwind CSS**: ^3.1.8 (Utility-first CSS framework)
- **@tailwindcss/forms**: ^0.5.3
- **@tailwindcss/typography**: ^0.5.9
- **@tailwindcss/aspect-ratio**: ^0.4.2
- **PostCSS**: ^8.4.16
- **Autoprefixer**: ^10.4.8

### JavaScript Libraries
- **Alpine.js**: ^3.13.2 (Lightweight JavaScript framework)
- **@alpinejs/persist**: ^3.14.9 (State persistence)
- **Axios**: ^0.27 (HTTP client)
- **Chart.js**: ^4.4.9 (Data visualization)
- **FullCalendar**: ^6.1.17 (Calendar and scheduling)
- **Flatpickr**: ^4.6.13 (Date picker)
- **GSAP**: ^3.12.5 (Animation library)
- **AOS**: ^2.3.4 (Animate on scroll)
- **Marked**: ^4.0.18 (Markdown parser)
- **KaTeX**: ^0.16.0 (Math rendering)
- **DOMPurify**: ^3.2.6 (XSS sanitization)

### Media Handling
- **PDF.js**: ^5.3.31 (PDF rendering)
- **Video.js**: ^8.6.1 (Video player)
- **videojs-contrib-quality-levels**: ^4.0.0
- **videojs-http-source-selector**: ^1.1.6
- **videojs-markers**: ^1.0.1
- **videojs-playlist**: ^5.2.0

### Rich Text Editor
- **TinyMCE**: ^7.8 (WYSIWYG editor)

## Third-Party Integrations

### AI & Machine Learning
- **OpenAI PHP Laravel**: ^0.17.1 (OpenAI API integration)
- **Prism PHP**: ^0.87.0 (AI framework)

### Payment Processing
- **Paystack**: Payment gateway (via custom service)

### Virtual Classroom
- **BigBlueButton API PHP**: ^2.3 (Video conferencing)

### File Processing
- **Intervention Image**: ^3.11 (Image manipulation)
- **Maatwebsite Excel**: ^3.1 (Excel import/export)
- **PHPOffice PHPWord**: ^1.3 (Word document generation)
- **Barryvdh Laravel DomPDF**: ^3.1 (PDF generation)
- **Smalot PDF Parser**: ^2.12 (PDF content extraction)
- **FPDF**: ^1.8 (PDF creation)
- **FPDI**: ^2.6 (PDF import)

### Cloud Storage
- **League Flysystem AWS S3**: ^3.0 (AWS S3 integration)
- **AWS SDK PHP**: (via Flysystem)

### Utilities
- **Guzzle HTTP**: ^7.2 (HTTP client)
- **Carbon**: Date/time manipulation (via Laravel)
- **Brick Money**: ^0.7.0 (Money handling)
- **League CommonMark**: ^2.7 (Markdown processing)
- **Jenssegers Agent**: ^2.6 (User agent detection)
- **Stevebauman Location**: ^7.5 (Geolocation)

### Activity Logging
- **Spatie Laravel Activity Log**: ^4.10 (User activity tracking)

### Authentication & Authorization
- **Lab404 Laravel Impersonate**: ^1.7 (User impersonation)

## Development Tools

### Build Tools
- **Vite**: ^3.0.0 (Frontend build tool)
- **Laravel Vite Plugin**: ^0.5.0
- **Node.js**: Required for frontend build

### Code Quality
- **Laravel Pint**: ^1.0 (PHP code style fixer)
- **PHPStan**: ^2.1 (Static analysis)
- **PHP CS Fixer**: Code formatting

### Debugging & Development
- **Barryvdh Laravel Debugbar**: ^3.15 (Debug toolbar)
- **Barryvdh Laravel IDE Helper**: ^3.5 (IDE autocomplete)
- **Laravel Sail**: ^1.0.1 (Docker development environment)
- **Laravel Boost**: ^1.1 (Performance optimization)

### Testing
- **PHPUnit**: ^9.5.10 (Unit testing)
- **Mockery**: ^1.4.4 (Mocking framework)
- **Faker PHP**: ^1.9.1 (Fake data generation)

## PHP Extensions Required
- **ext-dom**: XML/HTML manipulation
- **ext-imagick**: Image processing
- **ext-libxml**: XML parsing
- **ext-zip**: Archive handling

## Development Commands

### Composer (PHP Dependencies)
```bash
composer install          # Install PHP dependencies
composer update          # Update dependencies
composer dump-autoload   # Regenerate autoload files
```

### NPM (Frontend Dependencies)
```bash
npm install              # Install JavaScript dependencies
npm run dev             # Start Vite development server
npm run build           # Build for production
npm run copy-pdf-worker # Copy PDF.js worker files
```

### Artisan (Laravel CLI)
```bash
php artisan serve                    # Start development server
php artisan migrate                  # Run database migrations
php artisan db:seed                  # Seed database
php artisan queue:work               # Process queue jobs
php artisan telescope:install        # Install Telescope
php artisan storage:link             # Link storage directory
php artisan optimize                 # Cache configuration
php artisan optimize:clear           # Clear all caches
php artisan ide-helper:generate      # Generate IDE helper files
php artisan pint                     # Format code
```

### Testing
```bash
php artisan test                     # Run all tests
php artisan test --filter TestName   # Run specific test
vendor/bin/phpunit                   # Run PHPUnit directly
vendor/bin/phpstan analyse           # Run static analysis
```

### Database
```bash
php artisan migrate:fresh            # Drop all tables and re-migrate
php artisan migrate:fresh --seed     # Fresh migration with seeding
php artisan migrate:rollback         # Rollback last migration
php artisan migrate:status           # Show migration status
```

### Queue Management
```bash
php artisan queue:work               # Process queue jobs
php artisan queue:listen             # Listen for queue jobs
php artisan queue:restart            # Restart queue workers
php artisan queue:failed             # List failed jobs
php artisan queue:retry all          # Retry all failed jobs
```

### Cache Management
```bash
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear compiled views
```

## Environment Configuration

### Required Environment Variables
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `OPENAI_API_KEY` (OpenAI integration)
- `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY` (Payment processing)
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` (S3 storage)
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `BBB_SERVER_BASE_URL`, `BBB_SECRET` (BigBlueButton)
- `QUEUE_CONNECTION` (Queue driver: sync, database, redis)
- `SESSION_DRIVER`, `CACHE_DRIVER`

## Deployment Considerations
- PHP 8.1+ required
- Composer 2.x
- Node.js 16+ for frontend builds
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional, for caching and queues)
- AWS S3 bucket for file storage
- Queue worker process for background jobs
- Cron job for scheduled tasks (`php artisan schedule:run`)
- HTTPS required for production
- Proper file permissions for storage and bootstrap/cache directories
