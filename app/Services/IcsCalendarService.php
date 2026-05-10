<?php

namespace App\Services;

use App\Models\GeneralExam;
use Carbon\Carbon;

class IcsCalendarService
{
    public function generateIcs(GeneralExam $exam): string
    {
        $startDate = Carbon::parse($exam->start_datetime);
        $endDate = $exam->end_datetime 
            ? Carbon::parse($exam->end_datetime) 
            : $startDate->copy()->addMinutes($exam->duration_in_minutes ?? 60);

        $ics = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//All Academies//Examination System//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . md5($exam->id . $exam->title . $startDate->timestamp) . '@allacademies.com',
            'DTSTAMP:' . $this->formatDateTime(now()),
            'DTSTART:' . $this->formatDateTime($startDate),
            'DTEND:' . $this->formatDateTime($endDate),
            'SUMMARY:' . $this->escapeString($exam->title),
            'DESCRIPTION:' . $this->escapeString($this->getDescription($exam)),
            'LOCATION:Online - All Academies Platform',
            'STATUS:CONFIRMED',
            'SEQUENCE:0',
            'BEGIN:VALARM',
            'TRIGGER:-PT30M',
            'ACTION:DISPLAY',
            'DESCRIPTION:Exam starts in 30 minutes',
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $ics);
    }

    private function formatDateTime(Carbon $date): string
    {
        return $date->format('Ymd\THis\Z');
    }

    private function escapeString(string $text): string
    {
        $text = str_replace(["\r\n", "\n", "\r"], ' ', $text);
        $text = str_replace(['\\', ',', ';'], ['\\\\', '\\,', '\\;'], $text);
        return substr($text, 0, 200);
    }

    private function getDescription(GeneralExam $exam): string
    {
        $description = $exam->description ?? 'Examination';
        $description .= "\n\nAccess Code: " . $exam->access_code;
        $description .= "\nDuration: " . ($exam->duration_in_minutes ?? 'Unlimited') . " minutes";
        $description .= "\n\nJoin at: " . route('examinations-hub.take.join');
        
        return $description;
    }
}
