# Deployment Checklist

## Pre-Deployment

### 1. Code Review
- [ ] Review all modified files
- [ ] Check for any hardcoded values
- [ ] Verify error handling
- [ ] Review security implications

### 2. Testing
- [ ] Run all tests: `php vendor/bin/phpunit --filter="QuestionGeneratorTest|QuestionAvailabilityTest"`
- [ ] Verify all 19 tests pass
- [ ] Test dashboard manually
- [ ] Test API endpoints
- [ ] Generate ID map

### 3. Documentation Review
- [ ] Read EXAMINATION_FIXES.md
- [ ] Read API_QUICK_REFERENCE.md
- [ ] Read DASHBOARD_AND_ID_MAP.md
- [ ] Read IMPLEMENTATION_SUMMARY.md

## Deployment Steps

### 1. Backup
```bash
# Backup database
php artisan backup:run

# Backup current code
git stash save "pre-deployment-backup"
```

### 2. Deploy Code
```bash
# Pull latest changes
git pull origin main

# Install dependencies (if any new)
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Run Migrations
```bash
# No migrations needed for this update
# But verify database structure
php artisan migrate:status
```

### 4. Generate ID Map
```bash
# Generate initial ID map
php artisan academic:id-map

# Verify file created
ls -lh storage/app/academic_id_map.json
```

### 5. Set Permissions
```bash
# Ensure storage is writable
chmod -R 775 storage
chown -R www-data:www-data storage

# Verify
ls -la storage/app/
```

### 6. Test in Production
- [ ] Access dashboard: `/question-availability`
- [ ] Test with real subject data
- [ ] Generate ID map via dashboard
- [ ] Download ID map
- [ ] Test API endpoints
- [ ] Check availability for Chemistry
- [ ] Check availability for Financial Accounting

## Post-Deployment

### 1. Verification
```bash
# Run verification script
bash verify-fixes.sh

# Check logs for errors
tail -f storage/logs/laravel.log
```

### 2. User Access
- [ ] Verify owner can access dashboard
- [ ] Verify admin can access dashboard
- [ ] Verify other roles cannot access
- [ ] Test gate permissions

### 3. Monitoring
- [ ] Check Laravel logs
- [ ] Monitor API usage
- [ ] Track examination generation success
- [ ] Monitor question availability checks

### 4. User Training
- [ ] Show dashboard to owners/admins
- [ ] Explain how to check availability
- [ ] Demonstrate ID map usage
- [ ] Share documentation links

## Rollback Plan

If issues occur:

```bash
# Restore code
git stash pop

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Restart services
php artisan queue:restart
```

## Files to Deploy

### New Files (12)
1. app/Http/Controllers/Api/QuestionAvailabilityController.php
2. app/Console/Commands/GenerateAcademicIdMap.php
3. app/Livewire/QuestionAvailabilityChecker.php
4. resources/views/livewire/question-availability-checker.blade.php
5. tests/Unit/Services/QuestionGeneratorTest.php
6. tests/Feature/Api/QuestionAvailabilityTest.php
7. EXAMINATION_FIXES.md
8. API_QUICK_REFERENCE.md
9. COMPLETE_SUMMARY.md
10. DASHBOARD_AND_ID_MAP.md
11. IMPLEMENTATION_SUMMARY.md
12. verify-fixes.sh

### Modified Files (8)
1. app/Services/QuestionGenerator.php
2. app/Http/Controllers/ExaminationController.php
3. database/factories/MultipleChoiceQuestionFactory.php
4. database/factories/EssayQuestionFactory.php
5. database/factories/TrueOrFalseQuestionFactory.php
6. routes/api.php
7. routes/administrator.php
8. app/Providers/AuthServiceProvider.php

## Configuration

### Environment Variables
No new environment variables required.

### Permissions
```bash
# Storage directory must be writable
chmod -R 775 storage/app

# Ensure web server can write
chown -R www-data:www-data storage/app
```

### Queue Workers
No changes to queue configuration needed.

### Cron Jobs
No new cron jobs required.

## Testing Checklist

### Dashboard Tests
- [ ] Can access `/question-availability`
- [ ] Can select academic group
- [ ] Can select academic level
- [ ] Can select academic subject
- [ ] Can select topics
- [ ] Can check availability
- [ ] Results display correctly
- [ ] Sufficient questions show green
- [ ] Insufficient questions show red
- [ ] Breakdown displays properly
- [ ] Can generate ID map
- [ ] ID map displays on page
- [ ] Can download ID map

### API Tests
- [ ] POST `/api/questions/check-availability` works
- [ ] Returns correct JSON structure
- [ ] Validation errors work
- [ ] Relationship validation works
- [ ] GET `/api/questions/statistics` works
- [ ] Statistics return correct data

### ID Map Tests
- [ ] Command runs: `php artisan academic:id-map`
- [ ] File created in storage/app/
- [ ] JSON is valid
- [ ] Contains all groups
- [ ] Contains all levels
- [ ] Contains all subjects
- [ ] Contains all topics
- [ ] Contains all subtopics
- [ ] Question counts included
- [ ] Hierarchy preserved

### Question Generation Tests
- [ ] Can generate exam with Chemistry questions
- [ ] Can generate exam with Financial Accounting questions
- [ ] No "insufficient questions" errors when questions exist
- [ ] Proper error messages when truly insufficient
- [ ] Questions from subtopics included
- [ ] Questions from topics included

## Success Criteria

✅ All tests passing (19/19)
✅ Dashboard accessible to owners/admins
✅ API endpoints responding correctly
✅ ID map generating successfully
✅ Question generation working
✅ No errors in logs
✅ Documentation complete

## Support Contacts

**Technical Issues**:
- Check Laravel logs first
- Review documentation
- Test API endpoints directly
- Verify database relationships

**User Issues**:
- Refer to DASHBOARD_AND_ID_MAP.md
- Check user role permissions
- Verify gate access

## Maintenance

### Regular Tasks
- [ ] Regenerate ID map monthly
- [ ] Monitor question counts
- [ ] Review API usage logs
- [ ] Check examination generation success rate

### Updates
- [ ] Keep documentation updated
- [ ] Update ID map after structural changes
- [ ] Monitor for new requirements
- [ ] Gather user feedback

## Notes

- No database migrations required
- No breaking changes
- Backward compatible
- Can be deployed during business hours
- No downtime required
- Rollback is simple if needed

## Sign-Off

- [ ] Code reviewed
- [ ] Tests passing
- [ ] Documentation complete
- [ ] Deployment plan approved
- [ ] Rollback plan ready
- [ ] Team notified

**Deployed By**: _________________
**Date**: _________________
**Time**: _________________
**Status**: _________________

---

## Quick Commands Reference

```bash
# Run tests
php vendor/bin/phpunit --filter="QuestionGeneratorTest|QuestionAvailabilityTest"

# Generate ID map
php artisan academic:id-map

# Clear caches
php artisan optimize:clear

# Verify installation
bash verify-fixes.sh

# Check logs
tail -f storage/logs/laravel.log

# Check permissions
ls -la storage/app/
```
