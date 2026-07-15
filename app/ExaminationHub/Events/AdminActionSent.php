<?php

namespace App\ExaminationHub\Events;

use App\ExaminationHub\Models\ExamParticipantHeartbeat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminActionSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ExamParticipantHeartbeat $heartbeat,
        public string $action, // 'warning', 'terminate', 'force_submit', 'message', 'extend_time'
        public ?string $message = null,
        public array $data = []
    ) {}

    /**
     * Broadcast to both admin monitoring channel and participant's personal channel.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('exam-monitoring.' . $this->heartbeat->general_exam_id),
            new Channel('exam-participant.' . $this->heartbeat->session_token),
        ];
    }

    public function broadcastAs(): string
    {
        return 'admin.action';
    }

    public function broadcastWith(): array
    {
        return [
            'submission_id'  => $this->heartbeat->general_exam_submission_id,
            'session_token'  => $this->heartbeat->session_token,
            'action'         => $this->action,
            'message'        => $this->message,
            'data'           => $this->data,
            'timestamp'      => now()->toIso8601String(),
        ];
    }
}
