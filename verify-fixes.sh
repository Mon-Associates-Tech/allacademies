#!/bin/bash

# Examination Question Generation - Verification Script
# This script helps verify the fixes are working correctly

echo "=========================================="
echo "Examination Question Generation Fixes"
echo "Verification Script"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: artisan file not found. Please run this script from the Laravel root directory.${NC}"
    exit 1
fi

echo "Step 1: Running Tests"
echo "---------------------"
php vendor/bin/phpunit --filter="QuestionGeneratorTest|QuestionAvailabilityTest" --testdox

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ All tests passed!${NC}"
else
    echo -e "${RED}✗ Tests failed. Please check the output above.${NC}"
    exit 1
fi

echo ""
echo "Step 2: Checking API Routes"
echo "---------------------------"
php artisan route:list --path=api/questions 2>/dev/null

echo ""
echo "Step 3: Verifying Files"
echo "----------------------"

files=(
    "app/Services/QuestionGenerator.php"
    "app/Http/Controllers/ExaminationController.php"
    "app/Http/Controllers/Api/QuestionAvailabilityController.php"
    "database/factories/MultipleChoiceQuestionFactory.php"
    "database/factories/EssayQuestionFactory.php"
    "database/factories/TrueOrFalseQuestionFactory.php"
    "tests/Unit/Services/QuestionGeneratorTest.php"
    "tests/Feature/Api/QuestionAvailabilityTest.php"
)

all_exist=true
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file (missing)"
        all_exist=false
    fi
done

if [ "$all_exist" = false ]; then
    echo -e "${RED}Some files are missing!${NC}"
    exit 1
fi

echo ""
echo "Step 4: Documentation"
echo "--------------------"
docs=(
    "EXAMINATION_FIXES.md"
    "API_QUICK_REFERENCE.md"
    "COMPLETE_SUMMARY.md"
)

for doc in "${docs[@]}"; do
    if [ -f "$doc" ]; then
        echo -e "${GREEN}✓${NC} $doc"
    else
        echo -e "${YELLOW}⚠${NC} $doc (optional, not found)"
    fi
done

echo ""
echo "=========================================="
echo -e "${GREEN}Verification Complete!${NC}"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Test with real data from your database"
echo "2. Try the API endpoints:"
echo "   - POST /api/questions/check-availability"
echo "   - GET /api/questions/statistics"
echo "3. Generate an examination to verify the fixes"
echo ""
echo "For API examples, see: API_QUICK_REFERENCE.md"
echo "For technical details, see: EXAMINATION_FIXES.md"
echo ""
