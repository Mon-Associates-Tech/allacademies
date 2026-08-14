<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $templates = [
            [
                'slug'                => 'fee_payment_reminder',
                'name'                => 'School Fee Payment Reminder',
                'category'            => 'fee',
                'subject'             => 'Fee Payment Reminder – {{term_name}}',
                'body'                => "Dear {{recipient_name}},\n\nThis is a reminder that school fees for **{{student_name}}** for the **{{term_name}}** term are due.\n\n**Fee Summary:**\n- Total Amount: {{currency}} {{total_amount}}\n- Amount Paid: {{currency}} {{amount_paid}}\n- Balance Due: {{currency}} {{balance}}\n- Due Date: {{due_date}}\n\nKindly ensure payment is made before the due date to avoid any disruption to your ward's academic activities.\n\nFor payment enquiries, please contact the accounts office.\n\nRegards,\n{{school_name}} Accounts Office",
                'sms_body'            => "{{school_name}}: Fee reminder for {{student_name}}. Balance: {{currency}} {{balance}} due {{due_date}}. Contact accounts office for enquiries.",
                'available_variables' => json_encode(['recipient_name', 'student_name', 'term_name', 'currency', 'total_amount', 'amount_paid', 'balance', 'due_date', 'school_name']),
                'is_system'           => true,
                'is_active'           => true,
                'school_id'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'slug'                => 'overdue_fee_notice',
                'name'                => 'Overdue Fee Notice',
                'category'            => 'fee',
                'subject'             => 'URGENT: Overdue Fee Notice – {{student_name}}',
                'body'                => "Dear {{recipient_name}},\n\nThis is an urgent notice that the school fees for **{{student_name}}** for the **{{term_name}}** term are **overdue**.\n\n**Outstanding Balance:** {{currency}} {{balance}}\n**Original Due Date:** {{due_date}}\n\nPlease make payment immediately or contact the accounts office to discuss a payment arrangement.\n\nFailure to settle this balance may affect your ward's continued participation in school activities.\n\nRegards,\n{{school_name}} Accounts Office",
                'sms_body'            => "URGENT – {{school_name}}: Fees for {{student_name}} are OVERDUE. Balance: {{currency}} {{balance}}. Please pay immediately or call the accounts office.",
                'available_variables' => json_encode(['recipient_name', 'student_name', 'term_name', 'currency', 'total_amount', 'amount_paid', 'balance', 'due_date', 'school_name']),
                'is_system'           => true,
                'is_active'           => true,
                'school_id'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'slug'                => 'event_announcement',
                'name'                => 'Event Announcement',
                'category'            => 'event',
                'subject'             => '{{event_title}} – {{school_name}}',
                'body'                => "Dear {{recipient_name}},\n\nWe are pleased to inform you of an upcoming event at {{school_name}}.\n\n**{{event_title}}**\n\n{{event_description}}\n\n**Date:** {{event_date}}\n**Venue:** {{event_venue}}\n\nWe look forward to your participation.\n\nRegards,\n{{school_name}}",
                'sms_body'            => "{{school_name}}: {{event_title}} on {{event_date}} at {{event_venue}}. {{event_description}}",
                'available_variables' => json_encode(['recipient_name', 'school_name', 'event_title', 'event_description', 'event_date', 'event_venue']),
                'is_system'           => true,
                'is_active'           => true,
                'school_id'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'slug'                => 'general_reminder',
                'name'                => 'General Reminder',
                'category'            => 'general',
                'subject'             => 'Reminder from {{school_name}}',
                'body'                => "Dear {{recipient_name}},\n\n{{message_body}}\n\nRegards,\n{{school_name}}",
                'sms_body'            => "{{school_name}}: {{message_body}}",
                'available_variables' => json_encode(['recipient_name', 'school_name', 'message_body']),
                'is_system'           => true,
                'is_active'           => true,
                'school_id'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ];

        DB::table('message_templates')->insertOrIgnore($templates);
    }

    public function down(): void
    {
        DB::table('message_templates')
            ->where('is_system', true)
            ->whereNull('school_id')
            ->delete();
    }
};
