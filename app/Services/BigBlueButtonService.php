<?php

namespace App\Services;

use App\Models\Classroom\SessionParticipant;
use App\Models\Classroom\SessionRecording;
use App\Models\Classroom\VirtualSession;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\EndMeetingParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Exception;
use Illuminate\Support\Facades\Log;

class BigBlueButtonService
{
    protected BigBlueButton $bbb;

    public function __construct()
    {
        $this->bbb = new BigBlueButton(
            config('bigbluebutton.server_url'),
            config('bigbluebutton.secret')
        );
    }

    /**
     * Create a BigBlueButton meeting
     */
    public function createMeeting(VirtualSession $session): array
    {
        try {
            $createParams = new CreateMeetingParameters(
                $session->meeting_id,
                $session->title
            );

            // Set passwords
            $createParams->setAttendeePassword($session->attendee_password);
            $createParams->setModeratorPassword($session->moderator_password);

            // Set meeting settings
            $createParams->setRecord($session->auto_record);
            $createParams->setAutoStartRecording($session->auto_record);
            $createParams->setAllowStartStopRecording(true);
            $createParams->setMaxParticipants($session->max_participants);
            $createParams->setMuteOnStart($session->mute_on_start);
            $createParams->setWebcamsOnlyForModerator($session->webcams_only_for_moderator);
            $createParams->setGuestPolicy($session->guest_policy);

            // Set welcome message
            $welcomeMessage = $this->getWelcomeMessage($session);
            $createParams->setWelcomeMessage($welcomeMessage);

            // Set meeting metadata
            $createParams->addMeta('session-id', $session->id);
            $createParams->addMeta('school-id', $session->school_id);
            $createParams->addMeta('teacher-id', $session->teacher_id);
            $createParams->addMeta('subject', $session->academicSubject?->name ?? 'N/A');

            // Set logo and branding (optional)
            if (config('bigbluebutton.logo_url')) {
                $createParams->setLogo(config('bigbluebutton.logo_url'));
            }

            // Create the meeting
            $response = $this->bbb->createMeeting($createParams);

            if ($response->success()) {
                return [
                    'success' => true,
                    'meeting_id' => $response->getMeetingId(),
                    'internal_meeting_id' => $response->getInternalMeetingId(),
                    'attendee_pw' => $response->getAttendeePassword(),
                    'moderator_pw' => $response->getModeratorPassword(),
                    'create_time' => $response->getCreateTime(),
                    'raw_response' => $response->getRawXml()->asXML(),
                ];
            }

            return [
                'success' => false,
                'message' => $response->getMessage(),
            ];

        } catch (Exception $e) {
            Log::error('BBB Create Meeting Error', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get join URL for a participant
     */
    public function getJoinUrl(VirtualSession $session, SessionParticipant $participant): string
    {
        $joinParams = new JoinMeetingParameters(
            $session->meeting_id,
            $participant->full_name,
            $participant->isModerator() ? $session->moderator_password : $session->attendee_password
        );

        $joinParams->setUserId($participant->user_id);
        $joinParams->setRedirect(true);

        // Set user metadata
        $joinParams->addUserData('participant-id', $participant->id);
        $joinParams->addUserData('user-id', $participant->user_id);
        $joinParams->addUserData('role', $participant->role);

        return $this->bbb->getJoinMeetingURL($joinParams);
    }

    /**
     * Check if meeting is running
     */
    public function isMeetingRunning(string $meetingId): bool
    {
        try {
            return $this->bbb->isMeetingRunning($meetingId);
        } catch (Exception $e) {
            Log::error('BBB Is Meeting Running Error', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get meeting information
     */
    public function getMeetingInfo(string $meetingId): ?array
    {
        try {
            $infoParams = new GetMeetingInfoParameters($meetingId);
            $response = $this->bbb->getMeetingInfo($infoParams);

            if ($response->success()) {
                return [
                    'meeting_id' => $response->getMeetingId(),
                    'participant_count' => $response->getParticipantCount(),
                    'moderator_count' => $response->getModeratorCount(),
                    'attendees' => $this->formatAttendees($response->getAttendees()),
                    'start_time' => $response->getStartTime(),
                    'is_recording' => $response->isRecording(),
                ];
            }

            return null;
        } catch (Exception $e) {
            Log::error('BBB Get Meeting Info Error', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * End a meeting
     */
    public function endMeeting(VirtualSession $session): bool
    {
        try {
            $endParams = new EndMeetingParameters(
                $session->meeting_id,
                $session->moderator_password
            );

            $response = $this->bbb->endMeeting($endParams);
            return $response->success();

        } catch (Exception $e) {
            Log::error('BBB End Meeting Error', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get recordings for a meeting
     */
    public function getRecordings(string $meetingId): array
    {
        try {
            $recordingsParams = new GetRecordingsParameters();
            $recordingsParams->setMeetingId($meetingId);

            $response = $this->bbb->getRecordings($recordingsParams);

            if ($response->success()) {
                $recordings = [];
                foreach ($response->getRecords() as $record) {
                    $recordings[] = [
                        'record_id' => $record->getRecordId(),
                        'meeting_id' => $record->getMeetingId(),
                        'name' => $record->getName(),
                        'published' => $record->isPublished(),
                        'start_time' => $record->getStartTime(),
                        'end_time' => $record->getEndTime(),
                        'participants' => $record->getParticipantCount(),
                        'playback' => $this->formatPlaybackFormats($record->getPlaybackFormats()),
                        'metadata' => $record->getMetas(),
                    ];
                }
                return $recordings;
            }

            return [];
        } catch (Exception $e) {
            Log::error('BBB Get Recordings Error', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Delete recording
     */
    public function deleteRecording(string $recordingId): bool
    {
        try {
            $response = $this->bbb->deleteRecordings($recordingId);
            return $response->success();
        } catch (Exception $e) {
            Log::error('BBB Delete Recording Error', [
                'recording_id' => $recordingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Publish recording
     */
    public function publishRecording(string $recordingId, bool $publish = true): bool
    {
        try {
            $response = $this->bbb->publishRecordings($recordingId, $publish);
            return $response->success();
        } catch (Exception $e) {
            Log::error('BBB Publish Recording Error', [
                'recording_id' => $recordingId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Sync recordings from BBB to database
     */
    public function syncRecordings(VirtualSession $session): int
    {
        $recordings = $this->getRecordings($session->meeting_id);
        $syncedCount = 0;

        foreach ($recordings as $recordingData) {
            $recording = SessionRecording::updateOrCreate(
                [
                    'recording_id' => $recordingData['record_id'],
                    'virtual_session_id' => $session->id,
                ],
                [
                    'school_id' => $session->school_id,
                    'internal_recording_id' => $recordingData['meeting_id'],
                    'name' => $recordingData['name'],
                    'type' => 'bbb',
                    'status' => $recordingData['published'] ? 'published' : 'unpublished',
                    'playback_url' => $recordingData['playback']['url'] ?? null,
                    'duration_seconds' => $recordingData['playback']['length'] ?? null,
                    'recorded_at' => $recordingData['start_time'],
                    'published_at' => $recordingData['published'] ? now() : null,
                    'bbb_metadata' => $recordingData['metadata'] ?? [],
                    'playback_formats' => $recordingData['playback'] ?? [],
                ]
            );

            $syncedCount++;
        }

        return $syncedCount;
    }

    /**
     * Format attendees data
     */
    protected function formatAttendees($attendees): array
    {
        $formatted = [];
        foreach ($attendees as $attendee) {
            $formatted[] = [
                'user_id' => $attendee->getUserId(),
                'full_name' => $attendee->getFullName(),
                'role' => $attendee->getRole(),
                'is_presenter' => $attendee->isPresenter(),
                'is_listening_only' => $attendee->isListeningOnly(),
                'has_joined_voice' => $attendee->hasJoinedVoice(),
                'has_video' => $attendee->hasVideo(),
            ];
        }
        return $formatted;
    }

    /**
     * Format playback formats
     */
    protected function formatPlaybackFormats($formats): array
    {
        $formatted = [];
        foreach ($formats as $format) {
            $formatted[] = [
                'type' => $format->getType(),
                'url' => $format->getUrl(),
                'length' => $format->getLength(),
            ];
        }
        return $formatted;
    }

    /**
     * Get welcome message for meeting
     */
    protected function getWelcomeMessage(VirtualSession $session): string
    {
        $teacher = $session->teacher->user->name;
        $subject = $session->academicSubject?->name ?? 'Virtual Classroom';

        return <<<HTML
        <br>Welcome to <b>{$session->title}</b>!<br><br>
        <b>Teacher:</b> {$teacher}<br>
        <b>Subject:</b> {$subject}<br><br>
        For help on using BigBlueButton see these (short) <a href="https://bigbluebutton.org/html5"><u>tutorial videos</u></a>.<br><br>
        To join the audio bridge click the phone button. Use a headset to avoid causing background noise for others.
        HTML;
    }

    /**
     * Get moderator join URL
     */
    public function getModeratorJoinUrl(VirtualSession $session): string
    {
        $joinParams = new JoinMeetingParameters(
            $session->meeting_id,
            $session->teacher->user->name,
            $session->moderator_password
        );

        $joinParams->setUserId($session->teacher->user_id);
        $joinParams->setRedirect(true);
        $joinParams->addUserData('role', 'moderator');
        $joinParams->addUserData('teacher-id', $session->teacher_id);

        return $this->bbb->getJoinMeetingURL($joinParams);
    }

}
