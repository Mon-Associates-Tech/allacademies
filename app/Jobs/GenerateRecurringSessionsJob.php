<?php

namespace App\Jobs;

use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\VirtualSession;
use App\Notifications\VirtualSessionInvitationNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateRecurringSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        // Find all active recurring parent sessions
        $parentSessions = VirtualSession::where('is_recurring', true)
            ->where('recurrence_active', true)
            ->whereNull('parent_session_id')
            ->get();

        foreach ($parentSessions as $parent) {
            try {
                $this->generateFutureSessions($parent);
            } catch (\Exception $e) {
                Log::error('Failed to generate recurring sessions', [
                    'parent_session_id' => $parent->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function generateFutureSessions(VirtualSession $parent): void
    {
        // Check if recurrence should be stopped
        if ($parent->recurrence_end_date && now()->gt($parent->recurrence_end_date)) {
            $parent->update(['recurrence_active' => false]);
            return;
        }

        // Find the last generated child session
        $lastChild = $parent->childSessions()
            ->orderBy('scheduled_start', 'desc')
            ->first();

        $startFrom = $lastChild 
            ? Carbon::parse($lastChild->scheduled_start)
            : Carbon::parse($parent->scheduled_start);

        // Generate sessions for the next 8 weeks
        $generateUntil = now()->addWeeks(8);
        
        // Don't generate beyond the recurrence end date
        if ($parent->recurrence_end_date) {
            $generateUntil = min($generateUntil, Carbon::parse($parent->recurrence_end_date));
        }

        $currentDate = $startFrom;
        $generatedCount = 0;
        $maxGenerate = 50; // Safety limit per run

        while ($generatedCount < $maxGenerate) {
            $currentDate = $this->getNextOccurrence($currentDate, $parent);

            // Stop if we've reached the generation limit
            if ($currentDate->gt($generateUntil)) {
                break;
            }

            // Check if session already exists
            $exists = VirtualSession::where('parent_session_id', $parent->id)
                ->where('scheduled_start', $currentDate)
                ->exists();

            if (!$exists) {
                $this->createChildSession($parent, $currentDate);
                $generatedCount++;
            }
        }

        if ($generatedCount > 0) {
            Log::info("Generated {$generatedCount} recurring sessions for parent {$parent->id}");
        }
    }

    protected function getNextOccurrence(Carbon $date, VirtualSession $parent): Carbon
    {
        $next = $date->copy();

        switch ($parent->recurrence_pattern) {
            case 'daily':
                $next->addDays($parent->recurrence_interval);
                break;
                
            case 'weekly':
                $recurrenceDays = $parent->recurrence_days ?? [];
                $currentDayOfWeek = $next->dayOfWeekIso;
                $found = false;
                
                // Look for next day in the same week
                for ($i = 1; $i <= 7; $i++) {
                    $next->addDay();
                    $nextDayOfWeek = $next->dayOfWeekIso;
                    
                    if (in_array($nextDayOfWeek, $recurrenceDays)) {
                        $found = true;
                        break;
                    }
                }
                
                // If we've cycled through the week, add interval weeks
                if (!$found || ($parent->recurrence_interval > 1 && $next->dayOfWeekIso <= $currentDayOfWeek)) {
                    $next->addWeeks($parent->recurrence_interval - 1);
                    
                    // Find first occurrence day in the new week
                    while (!in_array($next->dayOfWeekIso, $recurrenceDays) && $next->dayOfWeek !== 0) {
                        $next->addDay();
                    }
                }
                break;
                
            case 'monthly':
                $next->addMonths($parent->recurrence_interval);
                break;
        }

        return $next;
    }

    protected function createChildSession(VirtualSession $parent, Carbon $scheduledStart): void
    {
        DB::transaction(function () use ($parent, $scheduledStart) {
            $scheduledEnd = $scheduledStart->copy()->addMinutes($parent->duration_minutes);

            $childSession = VirtualSession::create([
                'school_id' => $parent->school_id,
                'teacher_id' => $parent->teacher_id,
                'academic_level_id' => $parent->academic_level_id,
                'academic_group_id' => $parent->academic_group_id,
                'academic_subject_id' => $parent->academic_subject_id,
                'title' => $parent->title,
                'description' => $parent->description,
                'type' => $parent->type,
                'status' => 'scheduled',
                'scheduled_start' => $scheduledStart,
                'scheduled_end' => $scheduledEnd,
                'duration_minutes' => $parent->duration_minutes,
                'allow_guest_login' => $parent->allow_guest_login,
                'auto_record' => $parent->auto_record,
                'mute_on_start' => $parent->mute_on_start,
                'webcams_only_for_moderator' => $parent->webcams_only_for_moderator,
                'max_participants' => $parent->max_participants,
                'guest_policy' => $parent->guest_policy,
                'meeting_id' => 'session-' . time() . '-' . rand(1000, 9999),
                'attendee_password' => $parent->attendee_password,
                'moderator_password' => $parent->moderator_password,
                'parent_session_id' => $parent->id,
                'is_recurring' => false,
            ]);

            // Copy participants from parent
            foreach ($parent->participants as $parentParticipant) {
                $participant = SessionParticipant::create([
                    'virtual_session_id' => $childSession->id,
                    'user_id' => $parentParticipant->user_id,
                    'role' => $parentParticipant->role,
                    'status' => 'invited',
                    'full_name' => $parentParticipant->full_name,
                    'invited_at' => now(),
                    'invited_by' => $parentParticipant->invited_by,
                ]);

                // Send notification only if session is within 2 weeks
                if ($scheduledStart->lte(now()->addWeeks(2)) && $scheduledStart->isFuture()) {
                    $participant->user->notify(new VirtualSessionInvitationNotification($childSession, $participant));
                }
            }
        });
    }
}
