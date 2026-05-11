# Examinations Hub - Results Availability & Randomization Fixes

## Summary of Changes

This update addresses two critical issues in the examinations hub:

1. **Results Availability Configuration** - Administrators can now control when results are shown to participants
2. **Question Randomization** - Fixed randomization so each participant gets a unique question order

---

## 1. Results Availability Configuration

### Database Changes
- **Migration**: `2026_05_11_212246_add_results_release_datetime_to_general_exams_table.php`
- **New Column**: `results_release_datetime` (nullable datetime) on `general_exams` table

### Model Updates
- **GeneralExam.php**:
  - Added `results_release_datetime` to fillable array
  - Added `results_release_datetime` to casts as datetime
  - Updated `canShowResults()` method to support 'scheduled' visibility mode

### UI Changes
- **Exam Creation Form** (`create.blade.php`):
  - Added "Results Availability" section with 4 options:
    - ⚡ Immediately after submission
    - 📅 After exam end date
    - 🕐 Scheduled date & time (with datetime picker)
    - 🔒 Manual release by administrator
  - Conditional datetime field that shows only when "Scheduled" is selected

- **Exam Show Page** (`show.blade.php`):
  - Added "Results Availability" card displaying:
    - Visibility mode
    - Release date (if scheduled)
    - Current availability status (Yes/No with visual indicator)

### How It Works
```php
// In GeneralExam model
public function canShowResults(): bool
{
    return match ($this->result_visibility) {
        'immediate' => true,
        'after_due_date' => $this->isExpired(),
        'manual_release' => $this->results_released,
        'scheduled' => $this->results_release_datetime && now()->gte($this->results_release_datetime),
        default => false,
    };
}
```

---

## 2. Question Randomization Fix

### Problem
When `is_randomized` was true for a section, questions were shuffled on every page load, meaning:
- Participant A would see questions in order: 3, 1, 5, 2, 4
- Participant B would see questions in order: 3, 1, 5, 2, 4 (same order!)
- If Participant A refreshed, they'd see: 2, 4, 1, 3, 5 (different order!)

### Solution
Store the randomized order per participant in the submission record.

### Database Changes
- **Migration**: `2026_05_11_212731_add_randomized_question_order_to_general_exam_submissions_table.php`
- **New Column**: `randomized_question_order` (json, nullable) on `general_exam_submissions` table

### Model Updates
- **GeneralExamSubmission.php**:
  - Added `randomized_question_order` to fillable array
  - Added `randomized_question_order` to casts as array

### Controller Updates
- **ExamTakingController.php** - `section()` method:
  ```php
  // Handle randomization per participant
  if ($section->is_randomized && $questions->isNotEmpty()) {
      $randomizedOrder = $submission->randomized_question_order ?? [];
      $sectionKey = "section_{$section->id}";
      
      // If this section hasn't been randomized for this participant yet
      if (!isset($randomizedOrder[$sectionKey])) {
          $questionIds = $questions->pluck('id')->shuffle()->values()->toArray();
          $randomizedOrder[$sectionKey] = $questionIds;
          $submission->update(['randomized_question_order' => $randomizedOrder]);
      }
      
      // Reorder questions based on stored randomized order
      $orderedQuestions = collect();
      foreach ($randomizedOrder[$sectionKey] as $questionId) {
          $question = $questions->firstWhere('id', $questionId);
          if ($question) {
              $orderedQuestions->push($question);
          }
      }
      $questions = $orderedQuestions;
  }
  ```

### How It Works
1. When a participant first accesses a randomized section, the system:
   - Shuffles the question IDs
   - Stores the shuffled order in `randomized_question_order` JSON field
   - Uses format: `{"section_123": [45, 12, 78, 34, 56]}`

2. On subsequent visits to the same section:
   - Retrieves the stored order from the submission
   - Reorders questions according to the stored sequence
   - Ensures consistent order for that participant

3. Different participants get different orders:
   - Each submission has its own `randomized_question_order`
   - Participant A: `{"section_1": [3, 1, 5, 2, 4]}`
   - Participant B: `{"section_1": [2, 5, 1, 4, 3]}`

---

## Testing Checklist

### Results Availability
- [ ] Create exam with "Immediate" results - verify participants see results right away
- [ ] Create exam with "After end date" - verify results show only after exam ends
- [ ] Create exam with "Scheduled" date in future - verify results hidden until that time
- [ ] Create exam with "Manual release" - verify results hidden until admin releases
- [ ] Verify results availability card shows correct status on exam show page

### Question Randomization
- [ ] Create exam with randomized section
- [ ] Have Participant A take the exam - note question order
- [ ] Have Participant B take the exam - verify different question order
- [ ] Have Participant A refresh during exam - verify same question order maintained
- [ ] Check database - verify `randomized_question_order` is stored correctly
- [ ] Submit exam - verify grading works correctly with randomized questions

---

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback --step=2
```

---

## Files Modified

1. `database/migrations/2026_05_11_212246_add_results_release_datetime_to_general_exams_table.php` (NEW)
2. `database/migrations/2026_05_11_212731_add_randomized_question_order_to_general_exam_submissions_table.php` (NEW)
3. `app/Models/GeneralExam.php`
4. `app/Models/GeneralExamSubmission.php`
5. `app/Http/Controllers/Examinations/ExamTakingController.php`
6. `resources/views/examinations-hub/exams/create.blade.php`
7. `resources/views/examinations-hub/dashboard/show.blade.php`

---

## Notes

- Both features are backward compatible - existing exams will continue to work
- Default `result_visibility` should be set to 'immediate' for new exams
- Randomization only applies when `is_randomized` is true on a section
- The randomized order is stored per section, allowing different sections to have different randomization settings
