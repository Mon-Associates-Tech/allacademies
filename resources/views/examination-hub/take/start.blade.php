<x-layouts.exam>
    <div class="h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950 px-4 sm:px-6 overflow-y-auto">
        <div class="w-full max-w-5xl py-8">
            <div class="flex items-center justify-end mb-4">
                <x-snippets.theme-toggle />
            </div>
            
            @if($exam->starts_at && $exam->starts_at->isFuture())
                <div class="mb-6 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-sm overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-amber-800 dark:text-amber-200">Exam Opening Soon</h3>
                            <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                                This exam will open on {{ $exam->starts_at->format('F j, Y \a\t g:i A') }}.
                                You can join early and wait for the countdown to finish.
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-amber-800 dark:text-amber-200" id="countdown-display">--:--:--</div>
                            <div class="text-xs text-amber-600 dark:text-amber-400 mt-1">until start</div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                    <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Examination</p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $exam->title }}</h1>
                    @if($exam->instructions)
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $exam->instructions }}</p>
                    @endif
                </div>

                <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Sections</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">{{ $exam->sections->count() }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Questions</p>
                        <p class="mt tast-1 font-semibold text-slate-900 dark:text-white">{{ $exam->sections->sum('questions_count') }}</p>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm">
                        <p class="text-slate-500 dark:text-slate-400">Duration</p>
                        <p class="mt-1 font-semibold text-slate-900 dark:text-white">
                            {{ $exam->duration_in_minutes ? $exam->duration_in_minutes . ' minutes' : 'No limit' }}
                        </p>
                    </div>
                </div>

                @if($exam->proctoring_enabled)
                    <div class="px-6 py-4 bg-amber-50 dark:bg-amber-950/40 border-t border-amber-200 dark:border-amber-800 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <div class="text-sm">
                            <p class="font-semibold text-amber-800 dark:text-amber-300">This exam is proctored</p>
                            <p class="mt-0.5 text-amber-700 dark:text-amber-400">Your session will be monitored. The exam must be taken in fullscreen mode. Exiting fullscreen, switching tabs, or copying will be recorded as violations.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Sections</h2>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($exam->sections as $index => $section)
                        <div class="px-6 py-4 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    Section {{ $index + 1 }}: {{ $section->title }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $section->questions_count }} questions
                                    @if($section->time_limit_minutes)
                                        · {{ $section->time_limit_minutes }} min limit
                                    @endif
                                </p>
                            </div>

                            @php
                                $sectionUrl = route('examination-hub.take.section', [$exam, $index]);
                            @endphp
                            @if($exam->proctoring_enabled && $index === 0)
                                <button onclick="openAckModal('{{ $sectionUrl }}', true)"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-sm"
                                        @if($exam->starts_at && $exam->starts_at->isFuture()) disabled @endif>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                    Enter Fullscreen & Start
                                </button>
                            @elseif($exam->proctoring_enabled)
                                <span class="text-xs text-slate-400 dark:text-slate-500 italic">Complete previous section first</span>
                            @else
                                <button onclick="openAckModal('{{ $sectionUrl }}', false)"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-sm"
                                        @if($exam->starts_at && $exam->starts_at->isFuture()) disabled @endif>
                                    {{ $index === 0 ? 'Start' : 'Open' }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function enterFullscreenAndStart(url) {
            const el = document.documentElement;
            const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
            if (req) {
                req.call(el).then(() => { window.location.href = url; })
                            .catch(() => { window.location.href = url; }); // navigate anyway if denied
            } else {
                window.location.href = url;
            }
        }
        
        // Countdown timer for future exam start
        @if($exam->starts_at && $exam->starts_at->isFuture())
        function updateCountdown() {
            const startsAt = new Date('{{ $exam->starts_at->toISOString() }}');
            const now = new Date();
            const diff = startsAt.getTime() - now.getTime();
            
            if (diff <= 0) {
                document.getElementById('countdown-display').textContent = '00:00:00';
                // Enable buttons when countdown finishes
                document.querySelectorAll('button:not(#ack-continue-btn)').forEach(btn => {
                    btn.disabled = false;
                });
                clearInterval(countdownInterval);
                return;
            }
            
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('countdown-display').textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        }
        
        updateCountdown();
        const countdownInterval = setInterval(updateCountdown, 1000);
        @endif
    </script>
    <div id="ack-modal" style="display:none; position:fixed; inset:0; z-index:60; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
        <div style="background:#fff; max-width:28rem; margin:auto; padding:1.25rem; border-radius:6px;">
            <h3 style="font-weight:700; margin-bottom:0.5rem;">Confirm Instructions</h3>
            <p style="margin-bottom:0.75rem; color:#334155;">Please confirm you've read and understood the exam instructions. The timer will start once you confirm.</p>
            <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;"><input id="ack-checkbox" type="checkbox"> I have read and agree to the instructions</label>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button onclick="closeAckModal()" style="padding:0.5rem 0.75rem;">Cancel</button>
                <button id="ack-continue-btn" disabled style="padding:0.5rem 0.75rem; background:#0f172a; color:#fff;">Continue</button>
            </div>
        </div>
    </div>
    <script>
        let _ackTarget = null;
        let _ackProctor = false;
        function openAckModal(url, proctor) {
            _ackTarget = url; _ackProctor = !!proctor;
            document.getElementById('ack-modal').style.display = 'flex';
            const cb = document.getElementById('ack-checkbox');
            const btn = document.getElementById('ack-continue-btn');
            cb.checked = false; btn.disabled = true;
            cb.onchange = function () { btn.disabled = !this.checked; };
            btn.onclick = function () {
                closeAckModal();
                if (_ackProctor) {
                    enterFullscreenAndStart(_ackTarget);
                } else {
                    window.location.href = _ackTarget;
                }
            };
        }
        function closeAckModal() { document.getElementById('ack-modal').style.display = 'none'; }
    </script>

    <script>
        // Initialize proctoring if enabled for this exam
        const proctoringEnabled = @json($proctoringEnabled ?? false);
        
        if (proctoringEnabled) {
            // Configure the proctoring system BEFORE loading the script
            window.ExamProctorConfig = {
                eventUrl: @json(route('examination-hub.take.proctor.event', ['exam' => $exam])),
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                hardenedMode: @json($exam->hardened_mode ?? false),
                requireFullscreen: @json($exam->require_fullscreen ?? false),
                autoSubmitUrl: null // We'll handle submission via Livewire
            };
        }
    </script>

    @if($proctoringEnabled ?? false)
        @push('exam-scripts')
            <script src="{{ asset('js/exam-proctor.js') }}"></script>
        @endpush
    @endif
</x-layouts.exam>
