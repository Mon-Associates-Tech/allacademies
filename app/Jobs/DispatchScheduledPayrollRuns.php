<?php

namespace App\Jobs;

use App\Models\PayrollSchedule;
use App\Notifications\ScheduledPayrollDue;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchScheduledPayrollRuns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $schedules = PayrollSchedule::query()
            ->where('status', 'active')
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', now())
            ->get();
        
        foreach ($schedules as $schedule) {
            $accountants = $schedule->school->users()->where('role', 'accountant')->get();
            
            foreach ($accountants as $accountant) {
                $accountant->notify(new ScheduledPayrollDue($schedule));
            }
            
            $schedule->update([
                'last_run_at' => now(),
                'next_run_at' => $this->calculateNextRunDate($schedule),
            ]);
        }
    }

    protected function calculateNextRunDate(PayrollSchedule $schedule): ?Carbon
    {
        if ($schedule->frequency === 'one_time') {
            $schedule->update(['status' => 'completed']);
            return null;
        }
        
        $nextRun = Carbon::parse($schedule->next_run_at);
        
        return match($schedule->frequency) {
            'monthly' => $nextRun->addMonth(),
            'weekly' => $nextRun->addWeek(),
            'bi_weekly' => $nextRun->addWeeks(2),
            'quarterly' => $nextRun->addMonths(3),
            default => null,
        };
    }
}
