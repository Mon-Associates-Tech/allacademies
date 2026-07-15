<?php

namespace App\ExaminationHub\Events;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ExamParticipantHeartbeat $heartbeat,
        public string $previousStatus,
        public string $newStatus
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('exam-monitoring.'.$this->heartbeat->general_exam_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'participant' => $this->heartbeat->toLiveData(),
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
