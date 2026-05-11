# Question Availability API - Quick Reference

## Base URL
```
http://your-domain.com/api
```

## Endpoints

### 1. Check Question Availability
**POST** `/questions/check-availability`

Check if sufficient questions exist before generating an examination.

#### Request Body
```json
{
  "academic_subject_id": 1,           // Required
  "academic_group_id": 1,             // Optional
  "academic_level_id": 1,             // Optional
  "topic_ids": [1, 2, 3],             // Optional (defaults to all topics)
  "subtopic_ids": [5, 6],             // Optional
  "question_type": "multiple_choice_questions",  // Required
  "required_count": 20                // Required
}
```

#### Question Types
- `essay_questions`
- `multiple_choice_questions`
- `true_or_false_questions`

#### Success Response (200 OK)
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

#### Insufficient Questions Response (200 OK)
```json
{
  "success": true,
  "data": {
    "required_count": 50,
    "available_count": 15,
    "sufficient": false,
    ...
  }
}
```

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "academic_subject_id": [
      "The academic subject id field is required."
    ]
  }
}
```

#### Relationship Error (400)
```json
{
  "success": false,
  "message": "Subject does not belong to the specified level"
}
```

---

### 2. Get Question Statistics
**GET** `/questions/statistics?academic_subject_id={id}`

Get comprehensive question statistics for a subject.

#### Query Parameters
- `academic_subject_id` (required) - The subject ID

#### Success Response (200 OK)
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
          }
        ]
      }
    ]
  }
}
```

---

## Usage Examples

### Example 1: Check Chemistry MCQ Availability
```bash
curl -X POST http://localhost:8000/api/questions/check-availability \
  -H "Content-Type: application/json" \
  -d '{
    "academic_subject_id": 5,
    "question_type": "multiple_choice_questions",
    "required_count": 30
  }'
```

### Example 2: Check Specific Topics
```bash
curl -X POST http://localhost:8000/api/questions/check-availability \
  -H "Content-Type: application/json" \
  -d '{
    "academic_subject_id": 12,
    "topic_ids": [45, 46, 47],
    "question_type": "essay_questions",
    "required_count": 10
  }'
```

### Example 3: Check Subtopics Only
```bash
curl -X POST http://localhost:8000/api/questions/check-availability \
  -H "Content-Type: application/json" \
  -d '{
    "academic_subject_id": 8,
    "subtopic_ids": [101, 102, 103],
    "question_type": "true_or_false_questions",
    "required_count": 15
  }'
```

### Example 4: Get Subject Statistics
```bash
curl -X GET "http://localhost:8000/api/questions/statistics?academic_subject_id=5"
```

### Example 5: JavaScript/Axios
```javascript
// Check availability
const response = await axios.post('/api/questions/check-availability', {
  academic_subject_id: 5,
  question_type: 'multiple_choice_questions',
  required_count: 30
});

if (response.data.data.sufficient) {
  console.log('Sufficient questions available!');
  console.log(`Available: ${response.data.data.available_count}`);
} else {
  console.log('Insufficient questions!');
  console.log(`Need: ${response.data.data.required_count}`);
  console.log(`Have: ${response.data.data.available_count}`);
}
```

### Example 6: PHP/Laravel
```php
use Illuminate\Support\Facades\Http;

$response = Http::post('http://localhost:8000/api/questions/check-availability', [
    'academic_subject_id' => 5,
    'question_type' => 'multiple_choice_questions',
    'required_count' => 30,
]);

$data = $response->json()['data'];

if ($data['sufficient']) {
    echo "Sufficient questions available: {$data['available_count']}";
} else {
    echo "Insufficient questions. Need: {$data['required_count']}, Have: {$data['available_count']}";
}
```

---

## Best Practices

1. **Always Check Before Generating**: Call the check-availability endpoint before attempting to generate an examination to avoid failures.

2. **Handle Insufficient Questions**: When `sufficient` is `false`, show the user the breakdown to help them understand which topics need more questions.

3. **Use Statistics for Monitoring**: Regularly call the statistics endpoint to monitor the health of your question bank.

4. **Validate Relationships**: If providing `academic_group_id` or `academic_level_id`, ensure they match the subject's actual relationships.

5. **Default Behavior**: If you don't specify `topic_ids` or `subtopic_ids`, the API will check all topics in the subject.

---

## Error Handling

```javascript
try {
  const response = await axios.post('/api/questions/check-availability', data);
  
  if (response.data.success) {
    if (response.data.data.sufficient) {
      // Proceed with exam generation
    } else {
      // Show user which topics need more questions
      console.log('Breakdown:', response.data.data.breakdown);
    }
  }
} catch (error) {
  if (error.response?.status === 422) {
    // Validation errors
    console.error('Validation errors:', error.response.data.errors);
  } else if (error.response?.status === 400) {
    // Relationship errors
    console.error('Error:', error.response.data.message);
  } else {
    // Other errors
    console.error('Unexpected error:', error.message);
  }
}
```

---

## Testing

All endpoints have been tested with:
- ✓ 19 tests
- ✓ 62 assertions
- ✓ 100% passing

Run tests:
```bash
php vendor/bin/phpunit --filter="QuestionGeneratorTest|QuestionAvailabilityTest"
```
