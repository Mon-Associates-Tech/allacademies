<?php

namespace App\Livewire\Owner;

use App\Models\ArtisanCommandLog;
use App\Models\ArtisanCommandNamespace;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithPagination;
use ReflectionClass;

class ArtisanCommands extends Component
{
    use WithPagination;

    public $selectedCommand = '';
    public $selectedJob = '';
    public $commandArguments = [];
    public $output = '';
    public $isRunning = false;
    public $activeTab = 'commands';
    public $showNamespaceManager = false;
    public $showAuditLog = false;
    public $expandedLogId = null;

    public function mount(): void
    {
        // Sync namespaces on mount to detect new commands
        ArtisanCommandNamespace::syncNamespaces();
        
        $this->initializeArguments();
    }

    public function updatedSelectedCommand(): void
    {
        $this->initializeArguments();
        $this->output = '';
    }

    public function updatedSelectedJob(): void
    {
        $this->output = '';
    }

    private function initializeArguments(): void
    {
        if (!$this->selectedCommand) {
            $this->commandArguments = [];
            return;
        }

        $command = collect(Artisan::all())->get($this->selectedCommand);
        if (!$command) {
            $this->commandArguments = [];
            return;
        }

        $definition = $command->getDefinition();
        $this->commandArguments = [];

        foreach ($definition->getArguments() as $argument) {
            $this->commandArguments[$argument->getName()] = $argument->getDefault();
        }

        foreach ($definition->getOptions() as $option) {
            if ($option->getName() !== 'help' && $option->getName() !== 'quiet' && 
                $option->getName() !== 'verbose' && $option->getName() !== 'version' && 
                $option->getName() !== 'ansi' && $option->getName() !== 'no-ansi' && 
                $option->getName() !== 'no-interaction' && $option->getName() !== 'env') {
                $this->commandArguments['--' . $option->getName()] = $option->getDefault();
            }
        }
    }

    public function runCommand(): void
    {
        if (!$this->selectedCommand) {
            $this->output = 'Error: No command selected';
            return;
        }

        $this->isRunning = true;
        $this->output = '';
        $status = 'success';
        $errorMessage = null;

        try {
            $params = array_filter($this->commandArguments, fn($value) => $value !== null && $value !== '');
            
            Artisan::call($this->selectedCommand, $params);
            $this->output = Artisan::output();
        } catch (\Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            $this->output = 'Error: ' . $errorMessage;
        }

        // Log the command execution
        ArtisanCommandLog::create([
            'user_id' => auth()->id(),
            'command' => $this->selectedCommand,
            'arguments' => $this->commandArguments,
            'output' => $this->output,
            'status' => $status,
            'error_message' => $errorMessage,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'executed_at' => now(),
        ]);

        $this->isRunning = false;
    }

    public function runJob(): void
    {
        if (!$this->selectedJob) {
            $this->output = 'Error: No job selected';
            return;
        }

        $this->isRunning = true;
        $this->output = '';
        $status = 'success';
        $errorMessage = null;

        try {
            $jobClass = $this->selectedJob;
            
            if (!class_exists($jobClass)) {
                throw new \Exception("Job class {$jobClass} not found");
            }

            $reflection = new ReflectionClass($jobClass);
            $constructor = $reflection->getConstructor();
            
            if ($constructor && count($constructor->getParameters()) > 0) {
                $this->output = "Job '{$jobClass}' requires constructor parameters. Cannot dispatch without parameters.";
            } else {
                dispatch(new $jobClass());
                $this->output = "Job '{$jobClass}' has been dispatched successfully.";
            }
        } catch (\Exception $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            $this->output = 'Error: ' . $errorMessage;
        }

        // Log the job dispatch
        ArtisanCommandLog::create([
            'user_id' => auth()->id(),
            'command' => 'job:dispatch ' . class_basename($this->selectedJob),
            'arguments' => ['job_class' => $this->selectedJob],
            'output' => $this->output,
            'status' => $status,
            'error_message' => $errorMessage,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'executed_at' => now(),
        ]);

        $this->isRunning = false;
    }

    public function toggleNamespace($namespaceId): void
    {
        $namespace = ArtisanCommandNamespace::findOrFail($namespaceId);
        $namespace->update(['is_enabled' => !$namespace->is_enabled]);
        
        $this->dispatch('namespace-updated');
    }

    public function syncNamespaces(): void
    {
        ArtisanCommandNamespace::syncNamespaces();
        $this->dispatch('namespace-updated');
    }

    public function toggleLogOutput($logId): void
    {
        $this->expandedLogId = $this->expandedLogId === $logId ? null : $logId;
    }

    public function getCommandsProperty(): array
    {
        $enabledNamespaces = ArtisanCommandNamespace::getEnabledNamespaces();

        $commands = collect(Artisan::all())
            ->filter(function ($command) use ($enabledNamespaces) {
                $name = $command->getName();
                $namespace = explode(':', $name)[0];
                
                // Filter out completion commands
                if (str_starts_with($name, 'completion')) {
                    return false;
                }

                // Check if namespace is enabled
                return in_array($namespace, $enabledNamespaces);
            })
            ->sortBy('name')
            ->groupBy(function ($command) {
                $name = $command->getName();
                return explode(':', $name)[0];
            });

        return $commands->toArray();
    }

    public function getJobsProperty(): array
    {
        $jobsPath = app_path('Jobs');
        
        if (!File::exists($jobsPath)) {
            return [];
        }

        $jobs = collect(File::allFiles($jobsPath))
            ->map(function ($file) {
                $className = 'App\\Jobs\\' . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $file->getRelativePathname()
                );
                
                return [
                    'class' => $className,
                    'name' => class_basename($className),
                ];
            })
            ->sortBy('name')
            ->values()
            ->toArray();

        return $jobs;
    }

    public function getNamespacesProperty()
    {
        return ArtisanCommandNamespace::orderBy('sort_order')->get();
    }

    public function getRecentLogsProperty()
    {
        return ArtisanCommandLog::with('user')
            ->latest('executed_at')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.owner.artisan-commands');
    }
}
