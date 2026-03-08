# Project Structure

## Directory Organization

### Core Application (`app/`)
The main application logic following Laravel's MVC architecture with additional organizational layers.

#### Models (`app/Models/`)
- **User.php**: Central user model with role management, token subscriptions, and multi-tenancy
- **School.php**: Multi-tenant school entity with settings and relationships
- **Role-specific Models**: Student, Teacher, Librarian, Author, Administrator, StudentParent, Accountant
- **Academic Models**: AcademicYear, AcademicPeriod, AcademicLevel, AcademicGroup, AcademicSubject, AcademicTopic, AcademicSubtopic
- **Assessment Models**: Assessment, Question, Quiz, Examination, Assignment, QuizSession
- **Book Models**: Book, BookCategory, BookBorrowing, BookSubscription, BookInventory, BookReview
- **Chat Models**: OpenAiTokenPackage, UserTokenSubscription, OpenAiTokenUsageLog, AcademicChatSession, AcademicChatMessage
- **Communication Models**: Message, ChatMessage, Note, Forum models
- **Attendance Models**: Attendance records and tracking
- **Media Models**: MediaFile for file management

#### Controllers (`app/Http/Controllers/`)
Organized by feature and user role:
- Academic management controllers
- Assessment and quiz controllers
- Book and library controllers
- User and authentication controllers
- Payment and subscription controllers
- Chat and messaging controllers

#### Livewire Components (`app/Livewire/`)
Real-time interactive components organized by feature:
- `AcademicManagement/`: Academic structure management
- `Assessment/`: Quiz and examination interfaces
- `Books/`: Book catalog and management
- `Chats/`: Messaging and academic chat
- `Learning/`: Student learning interfaces
- `Notes/`: Note-taking and sharing
- `School/`: School administration
- `Students/`, `Teachers/`, `Librarians/`: Role-specific dashboards

#### Services (`app/Services/`)
Business logic layer:
- **AcademicChatService**: AI chat functionality
- **AssessmentService**: Assessment creation and grading
- **BookBasedLearningService**: Book-related learning features
- **ChatGPTService**: OpenAI integration
- **TokenSubscriptionService**: AI token management
- **PaystackService**: Payment processing
- **BigBlueButtonService**: Virtual session management
- **MediaService**: File upload and processing
- **SchoolContextService**: Multi-tenancy context management

#### Traits (`app/Traits/`)
Reusable model behaviors:
- **BelongsToSchool**: Multi-tenancy scoping
- **HasRoles**: Role management
- **HasAvatar**: Avatar handling
- **HasTeams**: Team membership
- **HasSubscriptionCycles**: Subscription management
- **Trackable**: Activity logging

#### Enums (`app/Enums/`)
Type-safe constants:
- **UserRole**: User role definitions
- **Grade**: Academic grade levels
- **PaymentStatus**: Payment states
- **PublishingStatus**: Content publishing states
- **SubscriptionStatus**: Subscription states

#### Jobs (`app/Jobs/`)
Background processing:
- **GenerateQuizJob**: AI-powered quiz generation
- **GenerateExaminationJob**: Examination creation
- **ConvertBookToAudioJob**: Audio book conversion
- **SendSessionRemindersJob**: Virtual session notifications
- **ResetMonthlySubscriptionCycles**: Subscription cycle management

#### Policies (`app/Policies/`)
Authorization rules for models:
- BookPolicy, AssessmentPolicy, StudentPolicy, TeacherPolicy, etc.

#### Middleware (`app/Http/Middleware/`)
Request filtering and processing

#### Events & Listeners (`app/Events/`, `app/Listeners/`)
Event-driven architecture for decoupled features

### Database (`database/`)
- **migrations/**: 200+ migration files for schema management
- **factories/**: Model factories for testing and seeding
- **seeders/**: Database seeding scripts

### Configuration (`config/`)
- **app.php**: Application settings
- **database.php**: Database connections
- **openai.php**: OpenAI configuration
- **bigbluebutton.php**: Virtual session settings
- **filesystems.php**: Storage configuration (S3, local)
- **services.php**: Third-party service credentials

### Routes (`routes/`)
Organized by user role and feature:
- **web.php**: Public and authenticated routes
- **api.php**: API endpoints
- **academic.php**: Academic management routes
- **student.php**: Student-specific routes
- **teacher.php**: Teacher-specific routes
- **librarian.php**: Library management routes
- **administrator.php**: Admin routes
- **author.php**: Author routes
- **parent.php**: Parent routes

### Views (`resources/views/`)
Blade templates organized by feature:
- **livewire/**: Livewire component views
- **components/**: Reusable UI components
- **layout/**: Layout templates
- **books/**, **students/**, **teachers/**: Feature-specific views
- **emails/**: Email templates

### Frontend Assets (`resources/`)
- **css/**: Tailwind CSS and custom styles
- **js/**: JavaScript modules (Alpine.js, Chart.js, PDF.js, Video.js)

### Public Assets (`public/`)
- **images/**: Static images
- **js/tinymce/**: Rich text editor
- **media/**: User-uploaded media

### Tests (`tests/`)
- **Feature/**: Integration tests
- **Unit/**: Unit tests

## Core Components & Relationships

### Multi-Tenancy Architecture
- **School Model**: Central tenant entity
- **BelongsToSchool Trait**: Automatic scoping to current school
- **SchoolScope**: Global scope for tenant isolation
- **SchoolContextService**: Manages current school context

### User & Role System
- **User Model**: Base user entity
- **Role Models**: Student, Teacher, Librarian, etc. (polymorphic relationship)
- **HasRoles Trait**: Role checking and management
- **UserRole Enum**: Type-safe role definitions

### Token Subscription System
- **OpenAiTokenPackage**: Available token packages
- **UserTokenSubscription**: User's active subscriptions
- **OpenAiTokenUsageLog**: Usage tracking
- **TokenSubscriptionStatus**: Subscription state management

### Assessment Hierarchy
- **Assessment**: Base assessment entity
- **Question**: Polymorphic (MultipleChoiceQuestion, EssayQuestion, TrueOrFalseQuestion)
- **QuizSession**: Student quiz attempts
- **AssessmentResponse**: Student answers

### Book Management
- **Book**: Digital book entity
- **BookCategory**: Book categorization
- **BookBorrowing**: Physical book loans
- **BookSubscription**: Digital book access
- **BookReadingProgress**: Reading tracking

## Architectural Patterns

### Repository Pattern
Services act as repositories, abstracting data access from controllers.

### Service Layer Pattern
Business logic encapsulated in service classes, keeping controllers thin.

### Observer Pattern
Model observers for automatic actions (e.g., creating role models on user creation).

### Strategy Pattern
Different payment strategies, assessment types, and subscription models.

### Factory Pattern
Model factories for testing and seeding.

### Trait Composition
Reusable behaviors through traits (BelongsToSchool, HasRoles, etc.).

### Event-Driven Architecture
Events and listeners for decoupled feature interactions.

### Multi-Tenancy Pattern
School-based data isolation with global scopes and context management.

### Livewire Component Pattern
Real-time, reactive UI components without writing JavaScript.

## Key Design Decisions

1. **Multi-Tenancy**: School-based isolation for SaaS model
2. **Polymorphic Relationships**: Flexible question types and role models
3. **Token-Based AI Access**: Subscription model for OpenAI usage
4. **Livewire for Interactivity**: Server-side rendering with reactive components
5. **Service Layer**: Separation of business logic from controllers
6. **Trait-Based Behaviors**: Code reuse across models
7. **Enum-Based Types**: Type safety for status and role values
8. **Job Queue**: Background processing for heavy operations
9. **Policy-Based Authorization**: Centralized permission logic
10. **AWS S3 Integration**: Scalable file storage
