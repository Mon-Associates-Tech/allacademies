<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Load Balancer Status</h3>
        <button wire:click="refresh" class="text-sm text-blue-600 hover:text-blue-800">
            Refresh
        </button>
    </div>

    @if (!$isCenter)
        <p class="text-sm text-gray-500">Load balancer is only available on center servers. This server is: {{ $role }}</p>
    @elseif ($lbMode === 'off')
        <p class="text-sm text-gray-500">Load balancer is currently disabled.</p>
        <p class="text-xs text-gray-400 mt-1">Enable via setup script: <code>--lb-mode local-first</code> or <code>--lb-mode central-first</code></p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Mode -->
            <div class="bg-gray-50 rounded p-3">
                <span class="text-xs text-gray-500 uppercase">Mode</span>
                <p class="font-medium">{{ ucfirst(str_replace('-', ' ', $lbMode)) }}</p>
            </div>

            <!-- Last checked -->
            <div class="bg-gray-50 rounded p-3">
                <span class="text-xs text-gray-500 uppercase">Last Checked</span>
                <p class="font-medium">{{ $lastChecked }}</p>
            </div>

            <!-- Local Backend -->
            <div class="bg-gray-50 rounded p-3">
                <span class="text-xs text-gray-500 uppercase">Local Backend</span>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $localBackend['status'] === 'UP' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <span class="font-medium">{{ ucfirst($localBackend['status']) }}</span>
                    <span class="text-xs text-gray-400">({{ $localBackend['sessions'] }} sessions)</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Check: {{ $localBackend['check'] }}</p>
            </div>

            <!-- Central Backend -->
            <div class="bg-gray-50 rounded p-3">
                <span class="text-xs text-gray-500 uppercase">Central Backend</span>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $centralBackend['status'] === 'UP' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    <span class="font-medium">{{ ucfirst($centralBackend['status']) }}</span>
                    <span class="text-xs text-gray-400">({{ $centralBackend['sessions'] }} sessions)</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Check: {{ $centralBackend['check'] }}</p>
            </div>

            <!-- Health: /up -->
            <div class="bg-gray-50 rounded p-3">
                <span class="text-xs text-gray-500 uppercase">Health /up</span>
                <p class="font-medium">
                    @if ($healthUp === 'ok')
                        <span class="text-green-600">OK</span>
                    @elseif ($healthUp === 'error')
                        <span class="text-red-600">Error</span>
                    @else
                        <span class="text-gray-400">Unreachable</span>
                    @endif
                </p>
            </div>

            <!-- Health: /lb-health -->
            <div class="bg-gray-50 rounded p-3">
                <span class="text-xs text-gray-500 uppercase">Health /lb-health</span>
                <p class="font-medium">
                    @if ($healthLb === 'ok')
                        <span class="text-green-600">OK</span>
                    @elseif ($healthLb === 'overloaded')
                        <span class="text-yellow-600">Overloaded</span>
                    @else
                        <span class="text-gray-400">Unreachable</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Rollback button -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <button wire:click="rollback"
                    wire:confirm="Are you sure you want to disable the load balancer? This will stop HAProxy and restore Nginx to ports 80/443."
                    class="text-sm text-red-600 hover:text-red-800">
                Disable Load Balancer (Rollback)
            </button>
            <p class="text-xs text-gray-400 mt-1">
                Or run: <code>php artisan exam:lb:rollback</code>
            </p>
        </div>
    @endif
</div>
