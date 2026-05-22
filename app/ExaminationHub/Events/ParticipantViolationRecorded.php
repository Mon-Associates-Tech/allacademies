<?php

namespace App\ExaminationHub\Events;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use App\ExaminationHub\Models\ExamProctoringLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantViolationRecorded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ExamParticipantHeartbeat $heartbeat,
        public ExamProctoringLog $log
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('exam-monitoring.' . $this->heartbeat->general_exam_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.violation';
    }

    public function broadcastWith(): array
    {
        return [
            'participant' => $this->heartbeat->toLiveData(),
            'violation'   => [
                'id'          => $this->log->id,
                'event_type'  => $this->log->event_type,
                'severity'    => $this->log->severity,
                'event_data'  => $this->log->event_data,
                'occurred_at' => $this->log->occurred_at->toIso8601String(),
            ],
        ];
    }
}
