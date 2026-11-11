<?php

namespace App\Livewire\Administrators;

use App\Services\DatabaseDependencyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SelectiveBackup extends Component
{
    use WithFileUploads;
    public $searchQuery = '';
    public $selectedTables = [];
    public $allTables = [];
    public $tableInfo = [];
    public $dependencyTree = null;
    public $selectedTableForTree = null;
    public $isBackingUp = false;
    public $backupProgress = 0;
    public $backupMessage = '';
    public $showChildren = false;
    public $childrenForSelected = [];

    // Restore properties
    public $showRestore = false;
    public $backupFiles = [];
    public $selectedBackupFile = '';
    public $uploadFile = null;
    public $isRestoring = false;
    public $restoreProgress = 0;
    public $restoreMessage = '';
    public $restoreMode = 'server'; // 'server' or 'upload'

    protected $rules = [
        'selectedTables' => 'array',
    ];

    public function mount(DatabaseDependencyService $service): void
    {
        $this->allTables = $service->getAllTables();
        $this->loadTableInfo();
    }

    public function loadTableInfo(): void
    {
        $service = app(DatabaseDependencyService::class);
        $this->tableInfo = $service->getTablesInfo($this->allTables);
    }

    public function search(string $query): void
    {
        $this->searchQuery = $query;
    }

    public function toggleTable(string $table): void
    {
        $service = app(DatabaseDependencyService::class);

        if (in_array($table, $this->selectedTables)) {
            // Remove table and any children that were only included because of this table
            $this->selectedTables = array_values(array_filter($this->selectedTables, function ($t) use ($table) {
                return $t !== $table;
            }));
            $this->selectedTableForTree = null;
            $this->dependencyTree = null;
        } else {
            // Add table and all its parents (required for restoration)
            $this->selectedTables[] = $table;
            $parents = $service->getAllParentTables($table);
            foreach ($parents as $parent) {
                if (!in_array($parent, $this->selectedTables)) {
                    $this->selectedTables[] = $parent;
                }
            }
            // Load dependency tree for the selected table
            $this->selectedTableForTree = $table;
            $this->dependencyTree = $service->getDependencyTree($table);
            $this->showChildren = true;
            $this->childrenForSelected = $service->getAllChildTables($table);
        }

        $this->selectedTables = array_values(array_unique($this->selectedTables));
    }

    public function selectAll(): void
    {
        $filtered = $this->getFilteredTables();
        $this->selectedTables = array_values(array_unique(array_merge($this->selectedTables, $filtered)));
    }

    public function deselectAll(): void
    {
        $this->selectedTables = [];
        $this->selectedTableForTree = null;
        $this->dependencyTree = null;
        $this->showChildren = false;
        $this->childrenForSelected = [];
    }

    public function includeChildren(): void
    {
        if (!$this->selectedTableForTree) {
            return;
        }

        $service = app(DatabaseDependencyService::class);
        $children = $service->getAllChildTables($this->selectedTableForTree);

        foreach ($children as $child) {
            if (!in_array($child, $this->selectedTables)) {
                $this->selectedTables[] = $child;
                // Also add parents of children
                $childParents = $service->getAllParentTables($child);
                foreach ($childParents as $parent) {
                    if (!in_array($parent, $this->selectedTables)) {
                        $this->selectedTables[] = $parent;
                    }
                }
            }
        }

        $this->selectedTables = array_values(array_unique($this->selectedTables));
        $this->showChildren = false;
    }

    public function viewDependencies(string $table): void
    {
        $service = app(DatabaseDependencyService::class);
        $this->selectedTableForTree = $table;
        $this->dependencyTree = $service->getDependencyTree($table);
        $this->childrenForSelected = $service->getAllChildTables($table);
        $this->showChildren = true;
    }

    public function closeDependencies(): void
    {
        $this->dependencyTree = null;
        $this->selectedTableForTree = null;
        $this->showChildren = false;
        $this->childrenForSelected = [];
    }

    public function performBackup(): void
    {
        if (empty($this->selectedTables)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Please select at least one table to backup.',
            ]);
            return;
        }

        $this->isBackingUp = true;
        $this->backupProgress = 0;
        $this->backupMessage = '';

        try {
            $service = app(DatabaseDependencyService::class);
            $this->backupProgress = 20;
            $this->backupMessage = 'Generating SQL dump...';

            $sql = $service->generateBackupSql($this->selectedTables);

            $this->backupProgress = 60;
            $this->backupMessage = 'Saving backup file...';

            $filename = 'database-backup-' . date('d-m-Y-H-i-s') . '.sql';
            Storage::disk('backups')->put($filename, $sql);

            $this->backupProgress = 100;
            $this->backupMessage = 'Backup completed successfully!';

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => "Backup completed! {$this->countSelectedTables()} tables backed up.",
            ]);
        } catch (\Exception $e) {
            $this->backupMessage = 'Backup failed: ' . $e->getMessage();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isBackingUp = false;
        }
    }

    public function downloadBackup()
    {
        if (empty($this->selectedTables)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Please select at least one table to backup.',
            ]);
            return;
        }

        try {
            $service = app(DatabaseDependencyService::class);
            $sql = $service->generateBackupSql($this->selectedTables);

            $filename = 'database-backup-' . date('d-m-Y-H-i-s') . '.sql';

            return response()->streamDownload(function () use ($sql) {
                echo $sql;
            }, $filename);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function getFilteredTables(): array
    {
        if (empty($this->searchQuery)) {
            return $this->allTables;
        }

        return array_filter($this->allTables, function ($table) {
            return str_contains(strtolower($table), strtolower($this->searchQuery));
        });
    }

    public function countSelectedTables(): int
    {
        return count($this->selectedTables);
    }

    public function isTableSelected(string $table): bool
    {
        return in_array($table, $this->selectedTables);
    }

    public function loadBackupFiles(): void
    {
        $files = Storage::disk('backups')->files('/');
        $this->backupFiles = collect($files)
            ->map(fn($path) => [
                'path' => $path,
                'name' => pathinfo($path, PATHINFO_FILENAME),
                'size' => Storage::disk('backups')->size($path),
                'modified' => Storage::disk('backups')->lastModified($path),
            ])
            ->sortByDesc('modified')
            ->values()
            ->toArray();
    }

    public function toggleRestore(): void
    {
        $this->showRestore = !$this->showRestore;
        if ($this->showRestore) {
            $this->loadBackupFiles();
        }
        $this->resetRestoreState();
    }

    public function resetRestoreState(): void
    {
        $this->selectedBackupFile = '';
        $this->uploadFile = null;
        $this->isRestoring = false;
        $this->restoreProgress = 0;
        $this->restoreMessage = '';
    }

    public function restoreFromServer(): void
    {
        if (empty($this->selectedBackupFile)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Please select a backup file to restore.',
            ]);
            return;
        }

        $this->isRestoring = true;
        $this->restoreProgress = 0;
        $this->restoreMessage = '';

        try {
            $this->restoreProgress = 10;
            $this->restoreMessage = 'Reading backup file...';

            $sql = Storage::disk('backups')->get($this->selectedBackupFile);

            $this->restoreProgress = 30;
            $this->restoreMessage = 'Parsing SQL statements...';

            $statements = $this->splitSqlStatements($sql);

            $this->restoreProgress = 50;
            $this->restoreMessage = 'Restoring database...';

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $successCount = 0;
            $errorCount = 0;
            foreach ($statements as $statement) {
                try {
                    DB::statement($statement);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            $this->restoreProgress = 100;
            $this->restoreMessage = "Restore completed! {$successCount} statements executed, {$errorCount} errors.";

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => "Restore completed! {$successCount} statements executed successfully. ({$errorCount} non-critical errors)",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->restoreMessage = 'Restore failed: ' . $e->getMessage();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Restore failed: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isRestoring = false;
        }
    }

    public function restoreFromUpload(): void
    {
        if (empty($this->uploadFile)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Please upload a SQL backup file.',
            ]);
            return;
        }

        $this->isRestoring = true;
        $this->restoreProgress = 0;
        $this->restoreMessage = '';

        try {
            $this->restoreProgress = 10;
            $this->restoreMessage = 'Reading uploaded file...';

            $sql = file_get_contents($this->uploadFile->getRealPath());

            $this->restoreProgress = 30;
            $this->restoreMessage = 'Parsing SQL statements...';

            $statements = $this->splitSqlStatements($sql);

            $this->restoreProgress = 50;
            $this->restoreMessage = 'Restoring database...';

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $successCount = 0;
            $errorCount = 0;
            foreach ($statements as $statement) {
                try {
                    DB::statement($statement);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            $this->restoreProgress = 100;
            $this->restoreMessage = "Restore completed! {$successCount} statements executed, {$errorCount} errors.";

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => "Restore completed! {$successCount} statements executed successfully. ({$errorCount} non-critical errors)",
            ]);

            $this->uploadFile = null;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->restoreMessage = 'Restore failed: ' . $e->getMessage();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Restore failed: ' . $e->getMessage(),
            ]);
        } finally {
            $this->isRestoring = false;
        }
    }

    protected function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $lines = explode("\n", $sql);
        $buffer = '';

        foreach ($lines as $line) {
            $trimmed = trim($line);
            // Skip comments and empty lines
            if (empty($trimmed) || str_starts_with($trimmed, '--')) {
                continue;
            }
            $buffer .= ' ' . $trimmed;
            // When we find a semicolon outside of quotes, it's the end of a statement
            if (str_ends_with($trimmed, ';')) {
                $statement = trim($buffer);
                if (!empty($statement)) {
                    $statements[] = $statement;
                }
                $buffer = '';
            }
        }

        return $statements;
    }

    public function deleteBackupFile(string $path): void
    {
        try {
            Storage::disk('backups')->delete($path);
            $this->loadBackupFiles();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Backup file deleted successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Failed to delete backup: ' . $e->getMessage(),
            ]);
        }
    }

    protected function formattedSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return number_format($bytes, 1) . ' ' . $units[$i];
    }

    public function getFormattedSizes(): array
    {
        $sizes = [];
        foreach ($this->backupFiles as $file) {
            $sizes[$file['path']] = $this->formattedSize($file['size']);
        }
        return $sizes;
    }

    public function render()
    {
        return view('livewire.administrators.selective-backup', [
            'filteredTables' => $this->getFilteredTables(),
            'formattedSizes' => $this->getFormattedSizes(),
        ]);
    }
}
