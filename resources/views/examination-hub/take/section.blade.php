<x-layouts.exam>
    @livewire('examination-hub.exam-section-taking', [
        'exam' => $exam,
        'submission' => $submission,
        'section' => $section,
        'sectionIndex' => $sectionIndex,
        'questions' => $questions,
    ])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sessionId = @json(request('proctoring_session_id'));
                const proctor = new window.ExamProctoring({
                    sessionId: sessionId,
                    endpoint: @json(route('examination-hub.take.proctor.event', ['exam' =>  $exam]))
                });
                proctor.init();

        });
    </script>
    <script>
        /**
         * exam-autosave.js (inline)
         *
         * Watches every answer input on the page and POSTs to saveResponse
         * 800 ms after the user stops changing an answer.
         * Shows a subtle status indicator so the user knows their work is safe.
         */
        (function () {
            'use strict';

            const SAVE_URL     = '{{ route('examination-hub.take.save-response', $exam) }}';
            const CSRF_TOKEN   = '{{ csrf_token() }}';
            const SECTION_IDX  = {{ $sectionIndex }};
            const DEBOUNCE_MS  = 800;

            // ── Status indicator ────────────────────────────────────────────────────
            const indicator = document.getElementById('autosave-indicator');

            function setStatus(state) {
                if (!indicator) return;
                const states = {
                    saving: { text: 'Saving…',  cls: 'text-amber-500' },
                    saved:  { text: '✓ Saved',  cls: 'text-emerald-600' },
                    error:  { text: '⚠ Not saved — retrying…', cls: 'text-red-500' },
                };
                const s = states[state] ?? states.saved;
                indicator.textContent = s.text;
                indicator.className   = `text-xs font-medium transition-all ${s.cls}`;
            }

            // ── Core save function ──────────────────────────────────────────────────
            async function saveResponse(questionId, response, retries = 2) {
                setStatus('saving');
                try {
                    const res = await fetch(SAVE_URL, {
                        method:  'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'Accept':        'application/json',
                            'X-CSRF-TOKEN':  CSRF_TOKEN,
                        },
                        body: JSON.stringify({
                            question_id:   questionId,
                            response:      response,
                            section_index: SECTION_IDX,
                        }),
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const json = await res.json();

                    if (json.status === 'already_submitted') {
                        // Exam was submitted in another tab — reload to the completed page
                        window.location.reload();
                        return;
                    }

                    setStatus('saved');
                } catch (err) {
                    if (retries > 0) {
                        setTimeout(() => saveResponse(questionId, response, retries - 1), 1500);
                    } else {
                        setStatus('error');
                        console.error('Auto-save failed:', err);
                    }
                }
            }

            // ── Debounce helper ─────────────────────────────────────────────────────
            const timers = {};
            function debouncedSave(questionId, response) {
                clearTimeout(timers[questionId]);
                timers[questionId] = setTimeout(() => saveResponse(questionId, response), DEBOUNCE_MS);
            }

            // ── Wire up all answer inputs ───────────────────────────────────────────
            document.addEventListener('DOMContentLoaded', function () {

                // Radio buttons (multiple choice, true/false)
                document.querySelectorAll('input[type="radio"][data-question-id]').forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        if (this.checked) {
                            debouncedSave(this.dataset.questionId, this.value);
                        }
                    });
                });

                // Textareas (essay, short answer)
                document.querySelectorAll('textarea[data-question-id]').forEach(function (textarea) {
                    textarea.addEventListener('input', function () {
                        debouncedSave(this.dataset.questionId, this.value);
                    });
                });

                // Text inputs (short answer fallback)
                document.querySelectorAll('input[type="text"][data-question-id]').forEach(function (input) {
                    input.addEventListener('input', function () {
                        debouncedSave(this.dataset.questionId, this.value);
                    });
                });
            });
        })();
    </script>
</x-layouts.exam>
