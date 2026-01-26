<?php

namespace App\Traits;

trait QuestionDataStandardization
{
    protected function standardizeQuestionData($data)
    {
        if (is_string($data)) {
            $decoded = json_decode($data);
            if (json_last_error() === JSON_ERROR_NONE) {
                return (object) [
                    'html' => $decoded->down ?? $decoded->html ?? $decoded->up ?? $data,
                    'down' => $decoded->down ?? null,
                    'up' => $decoded->up ?? null,
                ];
            }

            return (object) ['html' => $data];
        }

        if (is_object($data)) {
            return (object) [
                'html' => $data->down ?? $data->html ?? $data->up ?? '',
                'down' => $data->down ?? null,
                'up' => $data->up ?? null,
            ];
        }

        return (object) ['html' => ''];
    }
}
