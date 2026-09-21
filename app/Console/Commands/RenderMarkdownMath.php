<?php

namespace App\Console\Commands;

use App\Support\Mark;
use App\Support\MarkdownMathService;
use Illuminate\Console\Command;

class RenderMarkdownMath extends Command
{
    protected $signature = 'mark:render
        {model : Fully-qualified model class, e.g. "App\\Models\\MultipleChoiceQuestion"}
        {columns : Comma-separated Mark-cast columns, e.g. "question,option_a,option_b,option_c,option_d,option_e"}
        {--id= : Comma-separated primary key(s) to target instead of the whole table — use this to test on one record first}
        {--chunk=100}
        {--dry-run}';

    protected $description = 'Re-render existing Mark-cast columns through MarkdownMathService, treating the current stored value as raw markdown.';

    public function handle(MarkdownMathService $service): int
    {
        $modelClass = $this->argument('model');

        if (!class_exists($modelClass)) {
            $this->error("Model class {$modelClass} does not exist.");
            return self::FAILURE;
        }

        $columns = array_map('trim', explode(',', $this->argument('columns')));
        $dryRun = (bool) $this->option('dry-run');

        $idsOption = $this->option('id');
        $targetIds = $idsOption !== null
            ? array_filter(array_map('trim', explode(',', $idsOption)), fn ($v) => $v !== '')
            : null;
        $testing = $targetIds !== null;

        $total = 0;

        $query = $modelClass::query();

        if ($targetIds !== null) {
            $query->whereIn((new $modelClass)->getKeyName(), $targetIds);
        }

        $query->chunkById((int) $this->option('chunk'), function ($records) use ($service, $columns, $dryRun, $testing, &$total) {
            $raw = [];

            foreach ($records as $record) {
                foreach ($columns as $column) {
                    /** @var Mark|null $mark */
                    $mark = $record->{$column};
                    // Existing rows never had markdown parsed — 'down' currently
                    // holds the original typed-in text — that's the raw source
                    // to re-render from.
                    $raw["{$record->getKey()}:{$column}"] = $mark?->down ?? $mark?->up;
                }
            }

            $rendered = $service->renderMany($raw);

            foreach ($records as $record) {
                $dirty = false;

                foreach ($columns as $column) {
                    $key = "{$record->getKey()}:{$column}";
                    $source = $raw[$key];

                    if (!is_string($source) || trim($source) === '') {
                        continue;
                    }

                    if ($testing) {
                        $this->line("<comment>[{$record->getKey()}:{$column}]</comment> before:");
                        $this->line($source);
                        $this->line("<comment>[{$record->getKey()}:{$column}]</comment> after:");
                        $this->line($rendered[$key] ?? '(no output)');
                        $this->newLine();
                    }

                    $record->{$column} = new Mark($source, $rendered[$key]);
                    $dirty = true;
                }

                if ($dirty && !$dryRun) {
                    $record->saveQuietly();
                }

                $total++;
            }

            if (!$testing) {
                $this->info("Processed chunk ending at ID {$records->last()?->getKey()}");
            }
        });

        $this->info(($dryRun ? '[dry run] ' : '') . "Done — {$total} record(s) processed.");

        return self::SUCCESS;
    }
}
