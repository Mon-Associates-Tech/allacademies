# Examination Question Generation Fixes

## Summary

Fixed critical issues in the examination generation module where the system couldn't properly access questions from the database.

## Problems Identified

### 1. Overly Restrictive Query Filters in QuestionGenerator.php

**Issue**: When querying questions by subtopic, the code required BOTH `academic_subtopic_id` AND `academic_topic_id` to match:
```php
// OLD CODE - Line 147-148
->where('academic_subtopic_id', $subtopicId)
->where('academic_topic_id', $topicId)  // Too restrictive!
```

**Issue**: When querying topic-level questions, the code excluded questions with subtopics:
```php
// OLD CODE - Line 183
->whereIn('academic_topic_id', $topicIds)
->whereNull('academic_subtopic_id')  // Excludes questions with subtopics!
```

### 2. Incorrect Question Counting in ExaminationController.php

**Issue**: The `create` method used `withCount()` which doesn't properly count questions at both topic and subtopic levels, leading to incorrect question availability displays.

## Fixes Applied

### 1. QuestionGenerator.php (`app/Services/QuestionGenerator.php`)

#### Fix 1: Removed restrictive topic_id filter for subtopic queries (Line ~147)
```php
// FIXED CODE
$questions = DB::table($table)
    ->where('academic_subtopic_id', $subtopicId)
    // Removed: ->where('academic_topic_id', $topicId)
    ->whereNotIn('id', $usedQuestions)
    ->whereNotIn('id', $sectionQuestions)
    ->inRandomOrder()
    ->take($count)
    ->pluck('id')
    ->all();
```

#### Fix 2: Removed NULL subtopic filter for topic queries (Line ~183)
```php
// FIXED CODE
$topicQuestions = DB::table($table)
    ->whereIn('academic_topic_id', $topicIds)
    // Removed: ->whereNull('academic_subtopic_id')
    ->whereNotIn('id', $usedQuestions)
    ->whereNotIn('id', $sectionQuestions)
    ->inRandomOrder()
    ->take($remainingQuestionsNeeded)
    ->pluck('id')
    ->all();
```

#### Fix 3: Enhanced error logging
Added detailed logging to track:
- Total available questions in subtopics/topics
- Already used questions count
- Better error messages for debugging

### 2. ExaminationController.php (`app/Http/Controllers/ExaminationController.php`)

#### Fix: Proper question counting (Line ~70)
```php
// FIXED CODE - Direct DB queries for accurate counts
$topicEssayCount = \DB::table('essay_questions')
    ->where('academic_topic_id', $topic->id)
    ->count();
$topicMcqCount = \DB::table('multiple_choice_questions')
    ->where('academic_topic_id', $topic->id)
    ->count();
$topicTofCount = \DB::table('true_or_false_questions')
    ->where('academic_topic_id', $topic->id)
    ->count();
```

## New API Endpoints

### 1. Check Question Availability

**Endpoint**: `POST /api/questions/check-availability`

**Purpose**: Check if sufficient questions are available for examination generation

**Request Body**:
```json
{
  "academic_subject_id": 1,
  "academic_group_id": 1,  // Optional
  "academic_level_id": 1,  // Optional
  "topic_ids": [1, 2, 3],  // Optional
  "subtopic_ids": [5, 6],  // Optional
  "question_type": "multiple_choice_questions",  // Required: essay_questions, multiple_choice_questions, true_or_false_questions
  "required_count": 20  // Required
}
```

**Response** (Sufficient questions):
```json
{
  "success": true,
  "data": {
    "subject": {
      "id": 1,
      "name": "Mathematics",
      "code": "MATH101"
    },
    "question_type": "multiple_choice_questions",
    "required_count": 20,
    "available_count": 45,
    "sufficient": true,
    "breakdown": {
      "by_topic": [
        {
          "id": 1,
          "name": "Algebra",
          "available": 25
        },
        {
          "id": 2,
          "name": "Geometry",
          "available": 20
        }
      ],
      "by_subtopic": []
    }
  }
}
```

**Response** (Insufficient questions):
```json
{
  "success": true,
  "data": {
    "subject": {
      "id": 1,
      "name": "Financial Accounting",
      "code": "ACCT101"
    },
    "question_type": "essay_questions",
    "required_count": 50,
    "available_count": 15,
    "sufficient": false,
    "breakdown": {
      "by_topic": [
        {
          "id": 3,
          "name": "Financial Statements",
          "available": 10
        },
        {
          "id": 4,
          "name": "Accounting Principles",
          "available": 5
        }
      ],
      "by_subtopic": []
    }
  }
}
```

### 2. Get Question Statistics

**Endpoint**: `GET /api/questions/statistics?academic_subject_id=1`

**Purpose**: Get comprehensive question statistics for a subject

**Response**:
```json
{
  "success": true,
  "data": {
    "subject": {
      "id": 1,
      "name": "Chemistry",
      "code": "CHEM101"
    },
    "topics": [
      {
        "id": 1,
        "name": "Organic Chemistry",
        "essay_questions": 15,
        "multiple_choice_questions": 50,
        "true_or_false_questions": 20,
        "total_questions": 85,
        "subtopics": [
          {
            "id": 1,
            "name": "Alkanes",
            "essay_questions": 5,
            "multiple_choice_questions": 15,
            "true_or_false_questions": 8
          },
          {
            "id": 2,
            "name": "Alkenes",
            "essay_questions": 4,
            "multiple_choice_questions": 12,
            "true_or_false_questions": 6
          }
        ]
      }
    ]
  }
}
```

## Testing

### Running Tests

All tests have been created and are passing successfully.

#### Run All Question-Related Tests
```bash
php vendor/bin/phpunit --filter="QuestionGeneratorTest|QuestionAvailabilityTest"
```

#### Run Unit Tests Only
```bash
php vendor/bin/phpunit --filter=QuestionGeneratorTest
```
**Results**: 10 tests, 13 assertions - All passing ✓

#### Run API Feature Tests Only
```bash
php vendor/bin/phpunit --filter=QuestionAvailabilityTest
```
**Results**: 9 tests, 49 assertions - All passing ✓

### Test Coverage

#### Unit Tests (`tests/Unit/Services/QuestionGeneratorTest.php`)
✓ Test question selection from topics without subtopics
✓ Test question selection from subtopics
✓ Test mixed selection from both topics and subtopics
✓ Test insufficient questions exception
✓ Test no question reuse across sections
✓ Test essay question selection
✓ Test true/false question selection
✓ Test duplicate subtopic ID handling
✓ Test invalid subtopic ID handling
✓ Test unique question ID returns

#### Feature Tests (`tests/Feature/Api/QuestionAvailabilityTest.php`)
✓ Test availability check with sufficient questions
✓ Test availability check with insufficient questions
✓ Test availability with subtopics
✓ Test availability with multiple topics
✓ Test validation errors
✓ Test statistics endpoint
✓ Test group and level validation
✓ Test wrong level returns error
✓ Test default behavior when no topics specified

### Factories Created

All necessary factories have been implemented with proper data:

1. **MultipleChoiceQuestionFactory** - Generates MCQ with Mark-formatted questions and options
2. **EssayQuestionFactory** - Generates essay questions with Mark-formatted content
3. **TrueOrFalseQuestionFactory** - Generates true/false questions with Mark format

Existing factories used:
- AcademicTopicFactory
- AcademicSubjectFactory
- AcademicLevelFactory
- AcademicGroupFactory
- SchoolFactory (not needed for API tests)

## Usage Examples

### Example 1: Check if you can generate an exam with 30 MCQs from Chemistry

```bash
curl -X POST http://your-domain.com/api/questions/check-availability \
  -H "Content-Type: application/json" \
  -d '{
    "academic_subject_id": 5,
    "question_type": "multiple_choice_questions",
    "required_count": 30
  }'
```

### Example 2: Check specific topics for Financial Accounting

```bash
curl -X POST http://your-domain.com/api/questions/check-availability \
  -H "Content-Type: application/json" \
  -d '{
    "academic_subject_id": 12,
    "topic_ids": [45, 46, 47],
    "question_type": "essay_questions",
    "required_count": 10
  }'
```

### Example 3: Get all question statistics for a subject

```bash
curl -X GET "http://your-domain.com/api/questions/statistics?academic_subject_id=5"
```

## Benefits

1. **Accurate Question Counting**: System now correctly counts all available questions
2. **Better Error Messages**: Detailed logging helps identify exactly where questions are missing
3. **API Integration**: External systems can check question availability before attempting generation
4. **Prevents Failed Generations**: Users can verify sufficient questions exist before starting
5. **Debugging Support**: Comprehensive breakdown shows exactly which topics/subtopics have questions

## Migration Notes

No database migrations required. These are code-only fixes that work with the existing database structure.

## Files Modified

1. `/app/Services/QuestionGenerator.php` - Fixed question selection logic
2. `/app/Http/Controllers/ExaminationController.php` - Fixed question counting
3. `/routes/api.php` - Added new API routes

## Files Created

1. `/app/Http/Controllers/Api/QuestionAvailabilityController.php` - New API controller
2. `/tests/Unit/Services/QuestionGeneratorTest.php` - Unit tests
3. `/tests/Feature/Api/QuestionAvailabilityTest.php` - Feature tests

## Recommendations

1. **Before Generating Exams**: Always call the check-availability endpoint first
2. **Monitor Logs**: Check Laravel logs for detailed question availability information
3. **Regular Audits**: Use the statistics endpoint to monitor question bank health
4. **Add More Questions**: When availability is low, add more questions to affected topics/subtopics
