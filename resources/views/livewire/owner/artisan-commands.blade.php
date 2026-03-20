<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <!-- Header -->
    <div class="border-b border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">System Commands & Jobs</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Execute artisan commands and dispatch jobs</p>
            </div>
            <div class="flex gap-2">
                <button 
                    wire:click="syncNamespaces" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors"
                    title="Detect and register all available command namespaces"
                >
                    Sync Namespaces
                </button>
                <button 
                    wire:click="$toggle('showNamespaceManager')" 
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors"
                >
                    Manage Namespaces
                </button>
                <button 
                    wire:click="$toggle('showAuditLog')" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors"
                >
                    View Audit Log
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex gap-4 px-6" aria-label="Tabs">
            <button 
                wire:click="$set('activeTab', 'commands')" 
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'commands' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                Artisan Commands
            </button>
            <button 
                wire:click="$set('activeTab', 'jobs')" 
                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'jobs' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}"
            >
                Queue Jobs
            </button>
        </nav>
    </div>

    <div class="p-6">
        @if($activeTab === 'commands')
            <!-- Artisan Commands Tab -->
            <div class="space-y-6">
                <!-- Command Selection -->
                <div>
                    <label for="command" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Command
                    </label>
                    <select 
                        id="command" 
                        wire:model.live="selectedCommand" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="">-- Select a command --</option>
                        @foreach($this->commands as $namespace => $commands)
                            <optgroup label="{{ $namespace }}">
                                @foreach($commands as $command)
                                    <option value="{{ $command->getName() }}">
                                        {{ $command->getName() }}
                                        @if($command->getDescription())
                                            - {{ $command->getDescription() }}
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <!-- Command Arguments/Options -->
                @if($selectedCommand && count($commandArguments) > 0)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Arguments & Options</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($commandArguments as $name => $value)
                                <div>
                                    <label for="arg_{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ $name }}
                                    </label>
                                    <input 
                                        type="text" 
                                        id="arg_{{ $name }}" 
                                        wire:model="commandArguments.{{ $name }}" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                        placeholder="{{ is_bool($value) ? 'true/false' : 'Enter value' }}"
                                    >
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Run Button -->
                <div class="flex justify-end">
                    <button 
                        wire:click="runCommand" 
                        wire:loading.attr="disabled"
                        :disabled="!selectedCommand"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                    >
                        <svg wire:loading wire:target="runCommand" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="runCommand">Run Command</span>
                        <span wire:loading wire:target="runCommand">Running...</span>
                    </button>
                </div>
            </div>
        @else
            <!-- Queue Jobs Tab -->
            <div class="space-y-6">
                <!-- Job Selection -->
                <div>
                    <label for="job" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Job
                    </label>
                    <select 
                        id="job" 
                        wire:model.live="selectedJob" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    >
                        <option value="">-- Select a job --</option>
                        @foreach($this->jobs as $job)
                            <option value="{{ $job['class'] }}">{{ $job['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedJob)
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Note</h3>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    Jobs requiring constructor parameters cannot be dispatched from this interface. 
                                    Only jobs with no required parameters can be executed.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Run Button -->
                <div class="flex justify-end">
                    <button 
                        wire:click="runJob" 
                        wire:loading.attr="disabled"
                        :disabled="!selectedJob"
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-md disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                    >
                        <svg wire:loading wire:target="runJob" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="runJob">Dispatch Job</span>
                        <span wire:loading wire:target="runJob">Dispatching...</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Output Section -->
        @if($output)
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">Output</h3>
                    <button 
                        wire:click="$set('output', '')" 
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        Clear
                    </button>
                </div>
                <div class="bg-gray-900 dark:bg-black rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-green-400 font-mono whitespace-pre-wrap">{{ $output }}</pre>
                </div>
            </div>
        @endif
    </div>

    <!-- Namespace Manager Modal -->
    @if($showNamespaceManager)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Command Namespaces</h3>
                    <button wire:click="$toggle('showNamespaceManager')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            <strong>Info:</strong> Click "Sync Namespaces" to automatically detect all available command namespaces. 
                            User-defined commands are enabled by default, while Laravel core commands are disabled for safety.
                        </p>
                    </div>

                    <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Warning:</strong> Only enable namespaces you trust. Some commands can have destructive effects on your application.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- User-defined Namespaces -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">User-Defined Commands</h4>
                            <div class="space-y-2">
                                @foreach($this->namespaces->where('is_laravel_core', false) as $namespace)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $namespace->namespace }}</span>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $namespace->label }}</span>
                                            </div>
                                            @if($namespace->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $namespace->description }}</p>
                                            @endif
                                        </div>
                                        <button 
                                            wire:click="toggleNamespace({{ $namespace->id }})" 
                                            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $namespace->is_enabled ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-700' }}"
                                        >
                                            {{ $namespace->is_enabled ? 'Enabled' : 'Disabled' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Laravel Core Namespaces -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Laravel Core Commands</h4>
                            <div class="space-y-2">
                                @foreach($this->namespaces->where('is_laravel_core', true) as $namespace)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3">
                                                <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $namespace->namespace }}</span>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $namespace->label }}</span>
                                                @if(str_contains(strtolower($namespace->description), 'destructive'))
                                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded">DESTRUCTIVE</span>
                                                @endif
                                            </div>
                                            @if($namespace->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $namespace->description }}</p>
                                            @endif
                                        </div>
                                        <button 
                                            wire:click="toggleNamespace({{ $namespace->id }})" 
                                            class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $namespace->is_enabled ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-700' }}"
                                        >
                                            {{ $namespace->is_enabled ? 'Enabled' : 'Disabled' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Audit Log Modal -->
    @if($showAuditLog)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Command Execution Audit Log</h3>
                    <button wire:click="$toggle('showAuditLog')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Command</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Executed At</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($this->recentLogs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $log->user->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-mono text-gray-900 dark:text-white">{{ $log->command }}</div>
                                            @if($log->arguments && count($log->arguments) > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $log->formatted_arguments }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $log->status === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $log->executed_at->format('Y-m-d H:i:s') }}</div>
                                            <div class="text-xs text-gray-400">{{ $log->ip_address }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <button 
                                                wire:click="toggleLogOutput({{ $log->id }})"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                                            >
                                                {{ $expandedLogId === $log->id ? 'Hide Output' : 'View Output' }}
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Output Row (Hidden by default) -->
                                    @if($expandedLogId === $log->id)
                                        <tr class="bg-gray-50 dark:bg-gray-900">
                                            <td colspan="5" class="px-4 py-4">
                                                <div class="space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Command Output</h4>
                                                        <button wire:click="toggleLogOutput({{ $log->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="bg-gray-900 dark:bg-black rounded-lg p-4 overflow-x-auto max-h-96 overflow-y-auto">
                                                        <pre class="text-sm text-green-400 font-mono whitespace-pre-wrap">{{ $log->output ?: 'No output' }}</pre>
                                                    </div>
                                                    @if($log->error_message)
                                                        <div class="mt-2">
                                                            <h5 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-1">Error Message</h5>
                                                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-3">
                                                                <pre class="text-sm text-red-800 dark:text-red-300 font-mono whitespace-pre-wrap">{{ $log->error_message }}</pre>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No command executions logged yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $this->recentLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
