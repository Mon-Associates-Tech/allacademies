<x-layouts.app title="Importing Questions" action-link-text="" :action_link="''">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Dashboard' => route('dashboard'),
            'Academic Groups' => route('academic-groups.index'),
            'Importing Questions' => null,
        ]" />
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center"
                 id="import-status-card"
                 data-poll-url="{{ route('questions.import.status.poll', ['batch' => $batch]) }}"
                 data-back-url="{{ $backUrl }}">

                <div id="status-processing">
                    <svg class="mx-auto h-12 w-12 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Reading your document…
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        We're extracting and classifying questions from
                        <span class="font-medium">{{ $batch->original_filename }}</span>.
                        This can take a minute or two for larger documents — feel free to leave this page open
                        and come back, your progress isn't lost.
                    </p>
                    <p id="status-elapsed" class="mt-3 text-xs text-gray-400 dark:text-gray-500"></p>
                </div>

                <div id="status-failed" class="hidden">
                    <svg class="mx-auto h-12 w-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Import failed
                    </h2>
                    <p id="status-failed-message" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
                    <a href="{{ $backUrl }}"
                       class="mt-6 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Back to Import
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const card = document.getElementById('import-status-card');
            const pollUrl = card.dataset.pollUrl;

            const processingEl = document.getElementById('status-processing');
            const failedEl = document.getElementById('status-failed');
            const failedMessageEl = document.getElementById('status-failed-message');
            const elapsedEl = document.getElementById('status-elapsed');

            const startedAt = Date.now();
            const POLL_INTERVAL_MS = 3000;
            // Stop polling after this long even if the job is still running —
            // matches the job's own $timeout so we don't poll forever on a
            // genuinely stuck/abandoned job.
            const MAX_POLL_MS = 7 * 60 * 1000;

            function formatElapsed() {
                const seconds = Math.floor((Date.now() - startedAt) / 1000);
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return mins > 0 ? `${mins}m ${secs}s elapsed` : `${secs}s elapsed`;
            }

            function showFailed(message) {
                processingEl.classList.add('hidden');
                failedEl.classList.remove('hidden');
                failedMessageEl.textContent = message || 'An unknown error occurred during import.';
            }

            const elapsedTimer = setInterval(function () {
                elapsedEl.textContent = formatElapsed();
            }, 1000);

            function poll() {
                if (Date.now() - startedAt > MAX_POLL_MS) {
                    clearInterval(elapsedTimer);
                    showFailed('This is taking longer than expected. The import may still complete in the background — check back in a few minutes, or contact support if this persists.');
                    return;
                }

                fetch(pollUrl, { headers: { 'Accept': 'application/json' } })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Status check failed (' + response.status + ')');
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.status === 'completed' && data.redirect) {
                            clearInterval(elapsedTimer);
                            window.location.href = data.redirect;
                            return;
                        }

                        if (data.status === 'failed') {
                            clearInterval(elapsedTimer);
                            showFailed(data.message);
                            return;
                        }

                        // pending or processing — keep polling
                        setTimeout(poll, POLL_INTERVAL_MS);
                    })
                    .catch(function (err) {
                        // Transient network hiccup — keep trying rather than giving up immediately.
                        setTimeout(poll, POLL_INTERVAL_MS);
                    });
            }

            poll();
        });
    </script>
</x-layouts.app>
