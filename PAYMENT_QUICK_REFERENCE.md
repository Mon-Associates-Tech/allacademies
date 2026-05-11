# Payment System - Quick Reference

## Routes
```
/payments                      → Main payment management (toggle views)
/payments/setup                → Create/manage fee structures
/students/{id}/payments        → Student payment details
/transactions                  → Legacy transaction view
```

## Key Components

### PaymentManagement (Main Page)
- Toggle: Transactions ↔ Student Status
- Filters: Type, Status, Group, Level, Year, Period
- Actions: Setup Payments, View Details

### PaymentSetup (Fee Structures)
- Create fee structures
- Assign to groups/levels
- Auto-creates student records

### StudentPaymentDetails (Student View)
- Payment summary
- Payment records list
- Transaction history
- Add one-off payment
- Waive/discount payments

## Payment Statuses
- **Unpaid**: No payment made
- **Partial**: Some payment, balance remains
- **Paid**: Fully paid
- **Overdue**: Past due date, not paid
- **Waived**: Administratively waived

## Quick Actions

### Create Fee Structure
1. Payments → Setup Payments
2. Create Fee Structure
3. Fill form → Save
4. Records auto-created for students

### Add One-Off Payment
1. Student Management → Select Student
2. View Payments
3. Add One-Off Payment
4. Fill form → Create

### Waive Payment
1. Student Payments page
2. Find payment record
3. Click Waive → Confirm

### View Payment Status
1. Payments page
2. Toggle to "Student Status"
3. Filter as needed
4. Click "View Details" for student

## Database Tables
- `student_payment_records` → Expected payments per student
- `school_payments` → Actual transactions
- `school_payment_structures` → Fee structure templates

## Automatic Updates
When `SchoolPayment.status` → 'succeeded':
- Finds matching `StudentPaymentRecord`
- Updates `amount_paid`
- Recalculates `amount_remaining`
- Updates `status`

## Common Scenarios

### Scenario 1: Setup Term Fees
Setup → Create Structure → Select Group/Level → Save

### Scenario 2: Student Makes Payment
Payment Gateway → Success → Observer Updates Record

### Scenario 3: Special Fee for One Student
Student Page → Add One-Off Payment → Fill → Create

### Scenario 4: Waive Fee
Student Page → Find Payment → Waive → Confirm

## Filters Available
- Search (name, email, reference)
- Payment Type
- Status
- Academic Group
- Academic Level
- Academic Year
- Academic Period
- Date Range (transactions only)
- Subaccount (transactions only)

## Statistics Shown

### Transactions View
- Total Collected
- Pending Amount
- This Month
- Failed Count

### Student Status View
- Total Expected
- Total Collected
- Total Outstanding
- Overdue Count

## Permissions
- **Admin/Accountant**: Full CRUD access
- **Teacher**: Read-only access
- **Parent**: View own children's payments
- **Student**: View own payments

## Tips
1. Use filters to narrow down large lists
2. Toggle views to see different perspectives
3. Check overdue payments regularly
4. Use one-off payments for special cases
5. Document waiver reasons
6. Review payment structures each term
