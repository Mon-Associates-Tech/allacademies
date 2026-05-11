# Question Availability Dashboard & Academic ID Map

## Overview

Two new features have been added to help manage examination generation:

1. **Question Availability Checker Dashboard** - A visual interface for checking question availability
2. **Academic ID Map Generator** - A hierarchical map of all academic entities with their IDs

## Features

### 1. Question Availability Checker Dashboard

**Access**: Available to users with `owner` or `admin` roles

**Route**: `/question-availability`

**Features**:
- Visual form to select academic hierarchy (Group → Level → Subject → Topics)
- Real-time question availability checking
- Detailed breakdown by topic and subtopic
- Color-coded results (green for sufficient, red for insufficient)
- Shows exactly how many more questions are needed

**How to Use**:
1. Navigate to the dashboard (link in admin menu)
2. Select Academic Group (optional)
3. Select Academic Level (optional)
4. Select Academic Subject (required)
5. Select Topics (optional - leave empty for all topics)
6. Choose Question Type (MCQ, Essay, or True/False)
7. Enter Required Question Count
8. Click "Check Availability"

**Results Display**:
- ✓ Sufficient Questions: Green indicator with available count
- ✗ Insufficient Questions: Red indicator showing how many more needed
- Breakdown by Topic: Shows available questions per topic
- Breakdown by Subtopic: Shows available questions per subtopic

### 2. Academic ID Map

**Purpose**: Provides a complete hierarchical view of all academic entities with their IDs and question counts

**Generation Methods**:

#### Method 1: Via Dashboard
1. Go to Question Availability Dashboard
2. Click "Generate Map" button
3. View the hierarchical structure directly on the page
4. Download JSON file if needed

#### Method 2: Via Artisan Command
```bash
php artisan academic:id-map
```

**Output Location**: `storage/app/academic_id_map.json`

**Structure**:
```json
[
  {
    "id": 1,
    "name": "Primary School",
    "tag": "primary",
    "levels": [
      {
        "id": 1,
        "name": "Primary 1",
        "label": "P1",
        "group_id": 1,
        "group_name": "Primary School",
        "subjects": [
          {
            "id": 1,
            "name": "Mathematics",
            "code": "MATH101",
            "level_id": 1,
            "level_name": "Primary 1",
            "group_id": 1,
            "group_name": "Primary School",
            "topics": [
              {
                "id": 1,
                "name": "Numbers",
                "subject_id": 1,
                "subject_name": "Mathematics",
                "level_id": 1,
                "level_name": "Primary 1",
                "group_id": 1,
                "group_name": "Primary School",
                "questions": {
                  "essay": 10,
                  "multiple_choice": 50,
                  "true_or_false": 20,
                  "total": 80
                },
                "subtopics": [
                  {
                    "id": 1,
                    "name": "Counting",
                    "topic_id": 1,
                    "topic_name": "Numbers",
                    "subject_id": 1,
                    "subject_name": "Mathematics",
                    "level_id": 1,
                    "level_name": "Primary 1",
                    "group_id": 1,
                    "group_name": "Primary School",
                    "questions": {
                      "essay": 5,
                      "multiple_choice": 15,
                      "true_or_false": 8,
                      "total": 28
                    }
                  }
                ]
              }
            ]
          }
        ]
      }
    ]
  }
]
```

**Key Features**:
- Each entity includes its ID and name
- Each level includes parent hierarchy information
- Question counts at topic and subtopic levels
- Breakdown by question type (essay, MCQ, true/false)
- Total question count per topic/subtopic

## Access Control

### Question Availability Dashboard
**Allowed Roles**:
- Super Admin
- Owner
- Admin

**Gate**: `access-question-availability`

### Academic ID Map
**Generation**:
- Dashboard: Same as above
- Artisan Command: Anyone with server access

**Download**:
- Available to users who can access the dashboard

## Routes

```php
// Dashboard
GET /question-availability

// Download ID Map
GET /academic-id-map/download

// API Endpoints (used by dashboard)
POST /api/questions/check-availability
GET /api/questions/statistics
```

## Artisan Commands

### Generate Academic ID Map
```bash
php artisan academic:id-map

# Custom output location
php artisan academic:id-map --output=custom_name.json
```

**Output**:
```
Generating Academic ID Map...
Academic ID Map generated successfully!
File saved to: /path/to/storage/app/academic_id_map.json

+---------------------+-------+
| Entity              | Count |
+---------------------+-------+
| Academic Groups     | 3     |
| Academic Levels     | 12    |
| Academic Subjects   | 45    |
| Academic Topics     | 234   |
| Academic Subtopics  | 567   |
+---------------------+-------+
```

## Use Cases

### 1. Before Generating Examinations
Use the dashboard to verify sufficient questions exist:
```
1. Select subject: Chemistry
2. Select topics: Organic Chemistry, Inorganic Chemistry
3. Question type: Multiple Choice
4. Required: 30 questions
5. Click "Check Availability"
6. Result: 45 available ✓ Proceed with generation
```

### 2. Identifying Question Gaps
When insufficient questions:
```
Result shows:
- Topic 1: 10 questions available
- Topic 2: 3 questions available (need 7 more)
- Topic 3: 0 questions available (need 10 more)

Action: Add more questions to Topics 2 and 3
```

### 3. API Integration
External systems can check availability:
```bash
curl -X POST http://your-domain.com/api/questions/check-availability \
  -H "Content-Type: application/json" \
  -d '{
    "academic_subject_id": 5,
    "question_type": "multiple_choice_questions",
    "required_count": 30
  }'
```

### 4. Quick ID Reference
Use the ID map to quickly find entity IDs:
```json
// Need Chemistry subject ID?
// Search in ID map: "Chemistry" → ID: 12

// Need Organic Chemistry topic ID?
// Navigate: Chemistry → Topics → "Organic Chemistry" → ID: 45
```

## Dashboard Visual Hierarchy

The ID Map on the dashboard displays with color coding:
- **Blue**: Academic Groups
- **Green**: Academic Levels
- **Purple**: Academic Subjects
- **Orange**: Academic Topics
- **Gray**: Academic Subtopics

Each level shows:
- Entity ID (in colored badge)
- Entity name
- Additional info (code, tag, label)
- Question counts (for topics/subtopics)

## Benefits

1. **Proactive Planning**: Check availability before attempting generation
2. **Quick Reference**: Instant access to all entity IDs
3. **Visual Feedback**: Color-coded results for easy understanding
4. **Detailed Breakdown**: See exactly where questions are missing
5. **API Access**: Programmatic checking for integrations
6. **Hierarchical View**: Understand the complete academic structure
7. **Question Counts**: See question distribution across topics

## Troubleshooting

### Dashboard Not Accessible
- Check user role (must be owner or admin)
- Verify route is registered in `routes/administrator.php`
- Check gate definition in `AuthServiceProvider`

### ID Map Not Generating
- Ensure database has academic entities
- Check storage directory is writable
- Run: `php artisan storage:link`

### No Questions Showing
- Verify questions have `academic_topic_id` set
- Check questions are not soft-deleted
- Ensure proper database relationships

### API Errors
- Check API routes in `routes/api.php`
- Verify controller exists
- Check request validation rules

## Files Created/Modified

### New Files
1. `app/Console/Commands/GenerateAcademicIdMap.php` - Command to generate ID map
2. `app/Livewire/QuestionAvailabilityChecker.php` - Dashboard component
3. `resources/views/livewire/question-availability-checker.blade.php` - Dashboard view
4. `app/Http/Controllers/Api/QuestionAvailabilityController.php` - API controller

### Modified Files
1. `routes/administrator.php` - Added dashboard and download routes
2. `app/Providers/AuthServiceProvider.php` - Added access gate

## Future Enhancements

Potential improvements:
- Export ID map to CSV/Excel
- Filter ID map by group/level
- Search functionality in ID map
- Bulk question upload based on ID map
- Question distribution analytics
- Historical availability tracking
- Email alerts for low question counts

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connections
3. Test API endpoints directly
4. Review gate permissions
5. Check browser console for JavaScript errors
