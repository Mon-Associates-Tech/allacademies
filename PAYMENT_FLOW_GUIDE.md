# Payment Flow Implementation - Complete Guide

## Overview
This implementation provides a comprehensive payment management system for All Academies platform, allowing administrators and accountants to set up fee structures, track student payments, manage transactions, and handle one-off payments.

## Key Features Implemented

### 1. **Dual View Payment Management**
- **Transactions View**: See all payment transactions (existing functionality enhanced)
- **Student Payment Status View**: See all students with their payment obligations, amounts paid, and outstanding balances
- Toggle between views with a single button click

### 2. **Fee Structure Setup**
- Create fee structures for specific academic groups/levels or all students
- Set payment types (predefined or custom)
- Configure amounts, due dates, academic periods
- Enable/disable partial payments with minimum amounts
- Automatically creates payment records for applicable students

### 3. **Student Payment Details**
- Dedicated page showing all payments for a specific student
- View payment summary (expected, paid, outstanding, overdue)
- See complete transaction history
- Add one-off custom payments for individual students
- Waive payments with reason tracking
- Apply discounts

### 4. **Automatic Payment Tracking**
- When a transaction succeeds, automatically updates the student's payment record
- Tracks amount paid, remaining balance, and status
- Handles arrears from previous periods
- Overdue payment detection

## How to Use

### For Administrators/Accountants

#### Setting Up Fee Structures

1. Navigate to **Payments** from the sidebar
2. Click **"Setup Payments"** button
3. Click **"Create Fee Structure"**
4. Fill in the form:
   - **Name**: e.g., "Term 1 Tuition Fee"
   - **Payment Type**: Select from dropdown or choose "Custom Type"
   - **Amount**: Enter amount in GHS
   - **Due Date**: Set the payment deadline
   - **Academic Year/Period**: Optional - specify which period this applies to
   - **Academic Group/Level**: Optional - leave blank for all students, or select specific group/level
   - **Description**: Optional additional details
   - **Mandatory**: Check if this is a required payment
   - **Allow Partial Payment**: Check to allow students to pay in installments
   - **Active**: Uncheck to disable this fee structure
5. Click **"Create Fee Structure"**
6. System automatically creates payment records for all applicable students

#### Viewing Payment Status

1. Navigate to **Payments** from the sidebar
2. Click **"View Student Status"** button to toggle view
3. You'll see a table showing:
   - Student name and academic group/level
   - Payment type
   - Total amount expected
   - Amount paid so far
   - Amount remaining
   - Due date (with overdue indicator)
   - Payment status (Unpaid, Partial, Paid, Overdue, Waived)
4. Use filters to narrow down:
   - Search by student name
   - Filter by payment type
   - Filter by status
   - Filter by academic group/level
   - Filter by academic year/period
5. Click **"View Details"** to see full payment history for a student

#### Managing Individual Student Payments

1. From Student Management page, find the student
2. Click on the student to view details
3. Navigate to `/students/{id}/payments` or click "View Payments" button
4. You'll see:
   - Payment summary cards (Total Expected, Paid, Outstanding, Overdue)
   - List of all payment records with status
   - Complete transaction history
5. To add a one-off payment:
   - Click **"Add One-Off Payment"** button
   - Fill in:
     - Payment Type
     - Amount
     - Description (optional)
     - Due Date (optional)
   - Click **"Create Payment"**
6. To waive a payment:
   - Find the payment in the list
   - Click **"Waive"** button
   - Confirm the action

#### Viewing Transactions

1. Navigate to **Payments** from the sidebar
2. Default view shows all transactions
3. Filter by:
   - Search (reference, name, email)
   - Payment type
   - Status (Succeeded, Pending, Failed)
   - Academic group/level
   - Academic year/period
   - Receiving account (subaccount)
   - Date range
4. Click **"View"** to see transaction details

### For Teachers

- Teachers can view payment information (read-only access)
- Navigate to Payments to see transaction history
- Cannot create or modify fee structures

### For Parents

- Parents can view their children's payment obligations
- See payment history and outstanding balances
- Make payments online (when payment gateway is configured)

## Technical Details

### Database Schema

#### `student_payment_records` Table
```sql
- id
- school_id (FK to schools)
- student_id (FK to students)
- payment_structure_id (FK to school_payment_structures, nullable)
- academic_year_id (FK to academic_years, nullable)
- academic_period_id (FK to academic_periods, nullable)
- payment_type (string)
- description (text, nullable)
- total_amount (decimal)
- amount_paid (decimal, default 0)
- amount_remaining (decimal)
- currency (string, default 'GHS')
- due_date (date, nullable)
- status (enum: unpaid, partial, paid, overdue, waived)
- is_custom (boolean, default false)
- arrears_from_previous (decimal, default 0)
- discount_amount (decimal, default 0)
- waived (boolean, default false)
- waived_by (FK to users, nullable)
- waived_at (timestamp, nullable)
- waived_reason (text, nullable)
- metadata (json, nullable)
- timestamps
- soft_deletes
```

#### `school_payments` Table (Updated)
- Added `student_payment_record_id` (FK to student_payment_records, nullable)

### Models

#### StudentPaymentRecord
- **Location**: `app/Models/StudentPaymentRecord.php`
- **Key Methods**:
  - `updatePaymentStatus()`: Recalculates status based on payments
  - `addPayment($amount)`: Records a payment and updates status
  - `waive($user, $reason)`: Waives the payment
  - `isOverdue()`: Checks if payment is past due date
- **Scopes**:
  - `overdue()`: Payments past due date and not paid
  - `partiallyPaid()`: Payments with some amount paid
  - `unpaid()`: Payments with no amount paid
  - `paid()`: Fully paid payments

### Livewire Components

#### PaymentManagement
- **Route**: `/payments`
- **Purpose**: Main payment management interface with toggle views
- **Features**: Transactions view, Student status view, Filters, Statistics

#### StudentPaymentDetails
- **Route**: `/students/{student}/payments`
- **Purpose**: Detailed payment view for individual student
- **Features**: Payment records, Transaction history, One-off payments, Waive/Discount

#### PaymentSetup
- **Route**: `/payments/setup`
- **Purpose**: Create and manage fee structures
- **Features**: CRUD for fee structures, Automatic student record creation

### Observers

#### SchoolPaymentObserver
- **Location**: `app/Observers/SchoolPaymentObserver.php`
- **Purpose**: Automatically updates StudentPaymentRecord when SchoolPayment succeeds
- **Triggers**: When `status` changes to 'succeeded' or payment created as 'succeeded'
- **Logic**:
  1. Checks if payment is linked to a specific student payment record
  2. If not, finds matching record by student, payment type, and status
  3. Adds payment amount to record
  4. Updates status automatically

## Routes

```php
// Main payment management
Route::get('/payments', PaymentManagement::class)->name('admin.payments.index');

// Fee structure setup
Route::get('/payments/setup', PaymentSetup::class)->name('admin.payments.setup');

// Student payment details
Route::get('/students/{student}/payments', StudentPaymentDetails::class)->name('admin.students.payments');

// Transaction details (existing, kept for compatibility)
Route::get('/transactions', [SchoolPaymentController::class, 'index'])->name('admin.transactions.index');
Route::get('/transactions/{payment}', [SchoolPaymentController::class, 'show'])->name('admin.transactions.show');
```

## Workflow Examples

### Example 1: Setting Up Term Fees

1. Admin creates fee structure:
   - Name: "Term 1 Tuition"
   - Type: "Tuition"
   - Amount: 500.00 GHS
   - Due Date: 2026-02-15
   - Academic Period: Term 1
   - Academic Group: Primary
   - Academic Level: Grade 1

2. System creates StudentPaymentRecord for all Grade 1 students:
   - total_amount: 500.00
   - amount_paid: 0.00
   - amount_remaining: 500.00
   - status: unpaid
   - due_date: 2026-02-15

3. Parent makes payment of 200.00 GHS

4. SchoolPaymentObserver triggers:
   - Finds matching StudentPaymentRecord
   - Updates amount_paid: 200.00
   - Updates amount_remaining: 300.00
   - Updates status: partial

5. Parent makes second payment of 300.00 GHS

6. Observer triggers again:
   - Updates amount_paid: 500.00
   - Updates amount_remaining: 0.00
   - Updates status: paid

### Example 2: One-Off Payment for Damaged Property

1. Admin navigates to student's payment page
2. Clicks "Add One-Off Payment"
3. Fills form:
   - Payment Type: "Other"
   - Amount: 50.00 GHS
   - Description: "Damaged classroom window"
   - Due Date: 2026-05-01

4. System creates StudentPaymentRecord:
   - is_custom: true
   - All other fields as specified

5. Payment appears in student's payment list

### Example 3: Handling Arrears

1. Student has unpaid Term 1 fee of 500.00 GHS
2. Term 1 ends, payment becomes overdue
3. Admin creates Term 2 fee structure
4. When creating Term 2 payment record, admin can:
   - Manually add arrears_from_previous: 500.00
   - Or create a separate "Arrears" payment record

5. Student's Term 2 payment shows:
   - total_amount: 500.00 (Term 2 fee)
   - arrears_from_previous: 500.00
   - Total to pay: 1000.00 GHS

## Future Enhancements

### Planned Features

1. **Automated Arrears Calculation**
   - Scheduled job to identify overdue payments
   - Automatically add to next period as arrears

2. **Payment Plans**
   - Allow students to set up installment plans
   - Automatic reminders for upcoming installments

3. **Payment Reminders**
   - Email/SMS reminders before due date
   - Overdue payment notifications

4. **Bulk Operations**
   - Bulk waive payments
   - Bulk apply discounts
   - Bulk payment import

5. **Financial Reports**
   - Outstanding payments report
   - Collection rate by group/level
   - Revenue projections
   - Payment trends analysis

6. **Parent Portal Integration**
   - Parents can view all children's payments
   - Make payments directly
   - Download receipts

7. **Payment Gateway Integration**
   - Direct online payment from student/parent portal
   - Multiple payment methods
   - Automatic receipt generation

8. **Scholarship/Financial Aid**
   - Automatic discount application
   - Scholarship tracking
   - Financial aid workflow

## Troubleshooting

### Issue: Payment records not created for students

**Solution**: Check that:
1. Fee structure has correct academic group/level selected
2. Students exist in that group/level
3. Fee structure is marked as "Active"

### Issue: Payment not updating student record

**Solution**: Check that:
1. SchoolPaymentObserver is registered in AppServiceProvider
2. Payment status is 'succeeded'
3. Payment has student_id set
4. Matching StudentPaymentRecord exists

### Issue: Overdue payments not showing

**Solution**: Check that:
1. Due date is set on payment record
2. Due date is in the past
3. Status is not 'paid' or 'waived'

## Support

For issues or questions:
1. Check this documentation
2. Review the implementation summary: `PAYMENT_IMPLEMENTATION_SUMMARY.md`
3. Check model methods and relationships
4. Review observer logic for automatic updates

## Credits

Implemented by: Amazon Q
Date: April 28, 2026
Version: 1.0
