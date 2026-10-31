<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExamPackage;
use Illuminate\Support\Facades\DB;

/**
 * SyncManifestService — generates the sync manifest on central.
 *
 * The manifest lists all published exams with their latest revision,
 * state version, checksum, and payload size. Centers use this to
 * determine which exams need to be pulled.
 */
class SyncManifestService
{
    /**
     * Generate the manifest array.
     *
     * @return array{
     *     server_time: string,
     *     exams: array[]
     * }
     */
    public function generate(): array
    {
        $exams = $this->getExamList();

        return [
            'server_time' => now()->toIso8601String(),
            'exams' => $exams,
        ];
    }

    /**
     * Get the list of published exams with their latest package info.
     *
     * @return array[]
     */
    protected function getExamList(): array
    {
        return DB::table('exam_packages')
            ->select([
                'exam_uuid',
                'revision',
                'checksum',
                DB::raw('LENGTH(payload) as size'),
            ])
            ->whereIn('revision', function ($query) {
                $query->select(DB::raw('MAX(revision)'))
                    ->from('exam_packages as ep2')
                    ->whereColumn('ep2.exam_uuid', 'exam_packages.exam_uuid');
            })
            ->orderBy('exam_uuid')
            ->get()
            ->map(function ($row) {
                return [
                    'uuid' => $row->exam_uuid,
                    'revision' => (int) $row->revision,
                    'checksum' => $row->checksum,
                    'size' => (int) $row->size,
                ];
            })
            ->toArray();
    }

    /**
     * Get the state block for a specific exam.
     *
     * @return array|null
     */
    public function getStateBlock(string $examUuid): ?array
    {
        $exam = DB::table('general_exams')
            ->where('uuid', $examUuid)
            ->select([
                'uuid',
                'state_version',
                'status',
                'results_released',
                'results_released_at',
                'revision',
            ])
            ->first();

        if (!$exam) {
            return null;
        }

        return [
            'exam_uuid' => $exam->uuid,
            'state_version' => (int) $exam->state_version,
            'status' => $exam->status,
            'results_released' => (bool) $exam->results_released,
            'results_released_at' => $exam->results_released_at,
            'revision' => (int) $exam->revision,
        ];
    }
}
