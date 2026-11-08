<x-layouts.app pageName="Environment Editor">
    <div class="max-w-7xl mx-auto px-4 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Environment Variables</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $editableCount }} editable · {{ $lockedCount }} locked (database) · Restricted keys require email authorization
                </p>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800" style="border-radius: 2px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Alert --}}
        @if(session('error'))
            <div class="mb-4 px-4 py-2.5 text-sm text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800" style="border-radius: 2px;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Info Banner --}}
        <div class="mb-6 px-4 py-3 text-sm text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800" style="border-radius: 2px;">
            <strong>How it works:</strong>
            Only variables listed in <code class="font-mono bg-sky-100 dark:bg-sky-900 px-1 py-0.5">config/env-editor.php</code> can be edited here.
            Database variables (<code class="font-mono bg-sky-100 dark:bg-sky-900 px-1 py-0.5">DB_*</code>) are <strong>never editable</strong>.
            Variables marked <strong>RESTRICTED</strong> can only be edited by users with authorized emails (configured in the <code class="font-mono bg-sky-100 dark:bg-sky-900 px-1 py-0.5">restricted</code> section).
            Once a value is set, it is hidden in the UI — use the copy button to export it for terminal use.
        </div>

        {{-- Form --}}
        @push('head')
        <script>
            window.envEditorExpanded = @json(array_fill(0, count($grouped), true));
        </script>
        @endpush

        <div  x-data="{ expanded: window.envEditorExpanded || [] }">
            <form method="POST" action="{{ route('system.env.update') }}">
                @csrf

                @php $numericIndex = 0; @endphp
                @foreach($grouped as $groupName => $groupVars)
                    <div class="mb-4">
                        {{-- Group Header --}}
                        <button type="button"
                                @click="expanded[{{ $numericIndex }}] = !expanded[{{ $numericIndex }}]"
                                class="flex items-center justify-between w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-750 transition-colors text-left"
                                style="border-radius: 2px;">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                     :class="{ 'rotate-90': expanded[{{ $numericIndex }}] }"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $groupName }}</h2>
                                <span class="text-xs text-slate-400 dark:text-slate-500">({{ count($groupVars) }})</span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Copy All in Group --}}
                                <button type="button"
                                        @click="copyGroup('{{ $groupName }}')"
                                        class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                                        title="Copy all values in this group">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </button>

                        {{-- Group Content --}}
                        <div x-show="expanded[{{ $numericIndex }}]" x-collapse class="border-x border-b border-slate-200 dark:border-slate-700" style="border-radius: 0 0 2px 2px;">
                            @foreach($groupVars as $var)
                                <div class="flex items-center gap-3 px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">

                                    {{-- Variable Key --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <code class="text-xs font-mono font-semibold text-slate-800 dark:text-slate-200">{{ $var['key'] }}</code>

                                            {{-- Badges --}}
                                            @if($var['is_locked'])
                                                <span class="px-1.5 py-0.5 text-[10px] font-semibold text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-900/40 border border-red-200 dark:border-red-800" style="border-radius: 2px;">LOCKED</span>
                                            @elseif($var['is_editable'])
                                                <span class="px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800" style="border-radius: 2px;">EDITABLE</span>
                                            @else
                                                <span class="px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">READ-ONLY</span>
                                            @endif

                                            @if($var['is_sensitive'])
                                                <span class="px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/40 border border-amber-200 dark:border-amber-800" style="border-radius: 2px;">SENSITIVE</span>
                                            @endif

                                            @if($var['is_restricted'])
                                                <span class="px-1.5 py-0.5 text-[10px] font-semibold text-purple-700 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 border border-purple-200 dark:border-purple-800" style="border-radius: 2px;" title="Authorized: {{ implode(', ', $var['allowed_emails']) }}">RESTRICTED</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Value Display / Input --}}
                                    <div class="w-64 flex-shrink-0">
                                        @if($var['is_editable'] && !$var['is_locked'])
                                            @if($var['is_set'] && !$var['is_sensitive'])
                                                {{-- Value is set and not sensitive: show masked --}}
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-mono text-slate-400 dark:text-slate-500">●●●●●● (set)</span>
                                                    <input type="hidden" name="variables[{{ $var['key'] }}][key]" value="{{ $var['key'] }}">
                                                    <input type="hidden" name="variables[{{ $var['key'] }}][value]" value="{{ $var['value'] }}">
                                                    {{-- Reveal / Edit toggle --}}
                                                    <button type="button"
                                                            @click="$refs.edit{{ $var['key'] }}.classList.remove('hidden'); $refs.display{{ $var['key'] }}.classList.add('hidden');"
                                                            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                                            title="Edit value">
                                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div x-ref="edit{{ $var['key'] }}" class="hidden mt-1">
                                                    <input type="text"
                                                           name="variables[{{ $var['key'] }}][value]"
                                                           value="{{ $var['value'] }}"
                                                           class="w-full px-2 py-1 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                                                           style="border-radius: 2px;">
                                                </div>
                                            @elseif($var['is_set'] && $var['is_sensitive'])
                                                {{-- Sensitive and set: always masked --}}
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-mono text-amber-500 dark:text-amber-400">●●●●●● (sensitive)</span>
                                                    <input type="hidden" name="variables[{{ $var['key'] }}][key]" value="{{ $var['key'] }}">
                                                    <input type="hidden" name="variables[{{ $var['key'] }}][value]" value="{{ $var['value'] }}">
                                                    <button type="button"
                                                            @click="$refs.edit{{ $var['key'] }}.classList.remove('hidden'); $refs.display{{ $var['key'] }}.classList.add('hidden');"
                                                            class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                                            title="Change value">
                                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div x-ref="edit{{ $var['key'] }}" class="hidden mt-1">
                                                    <input type="text"
                                                           name="variables[{{ $var['key'] }}][value]"
                                                           placeholder="Enter new value..."
                                                           class="w-full px-2 py-1 text-xs font-mono bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-700 text-slate-800 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500"
                                                           style="border-radius: 2px;">
                                                </div>
                                            @else
                                                {{-- Not set: show input --}}
                                                <input type="text"
                                                       name="variables[{{ $var['key'] }}][key]"
                                                       value="{{ $var['key'] }}"
                                                       class="hidden">
                                                <input type="text"
                                                       name="variables[{{ $var['key'] }}][value]"
                                                       placeholder="Not set — enter value..."
                                                       class="w-full px-2 py-1 text-xs font-mono bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                                       style="border-radius: 2px;">
                                            @endif
                                        @else
                                            {{-- Not editable: show value or masked --}}
                                            @if($var['is_set'])
                                                @if($var['is_sensitive'])
                                                    <span class="text-xs font-mono text-amber-500 dark:text-amber-400">●●●●●● (sensitive)</span>
                                                @else
                                                    <span class="text-xs font-mono text-slate-400 dark:text-slate-500">●●●●●● (set)</span>
                                                @endif
                                            @else
                                                <span class="text-xs font-mono text-slate-300 dark:text-slate-600">— not set —</span>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Copy Button --}}
                                    @if($var['is_set'])
                                        <button type="button"
                                                @click="copyValue('{{ $var['key'] }}', '{{ addslashes($var['value']) }}')"
                                                class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                                                title="Copy KEY=VALUE to clipboard">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @php $numericIndex++; @endphp
                @endforeach

                {{-- Save Button --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="reset"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                            style="border-radius: 2px;">
                        Reset
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white transition-colors"
                            style="border-radius: 2px; background: linear-gradient(135deg, #0369a1, #38bdf8);">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>


        {{-- Copy All Button --}}
        <div class="mt-6 flex items-center justify-between">
            <p class="text-xs text-slate-400 dark:text-slate-500">
                Tip: Use the copy buttons to export values for terminal provisioning.
            </p>
            <button type="button"
                    onclick="copyAllSet()"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                    style="border-radius: 2px;">
                Copy All Set Values
            </button>
        </div>

    </div>

    {{-- Toast Notification --}}
    <div x-data="{ show: false, message: '' }"
         x-show="show"
         x-transition
         class="fixed bottom-4 right-4 px-4 py-2 text-sm font-semibold text-white bg-slate-800 dark:bg-slate-700 shadow-lg z-50"
         style="border-radius: 2px;"
         x-init="window.copyToast = (msg) => { show = true; message = msg; setTimeout(() => show = false, 2000); }">
        <span x-text="message"></span>
    </div>

    <script>
        // Global copy helpers — no Alpine dependency
        @php
            $allSetVars = array_filter($variables, fn($v) => $v['is_set']);
            $groupMapKeys = array_flip(array_merge(...array_values(config('env-editor.groups', []))));
            $allVarsJson = json_encode($allSetVars, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            $groupMapJson = json_encode($groupMapKeys, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        @endphp
        const envEditorAllVariables = {{ $allVarsJson }};
        const envEditorGroupMap = {{ $groupMapJson }};

        function copyValue(key, value) {
            const text = key + '=' + value;
            navigator.clipboard.writeText(text).then(() => {
                window.copyToast('Copied: ' + key);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                window.copyToast('Copied: ' + key);
            });
        }

        function copyGroup(groupName) {
            const groupVars = envEditorAllVariables.filter(v => envEditorGroupMap[v.key] === groupName);
            if (groupVars.length === 0) {
                window.copyToast('No set values in this group');
                return;
            }
            const text = groupVars.map(v => v.key + '=' + v.value).join('\n');
            navigator.clipboard.writeText(text).then(() => {
                window.copyToast('Copied ' + groupVars.length + ' variable(s) from ' + groupName);
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                window.copyToast('Copied ' + groupVars.length + ' variable(s) from ' + groupName);
            });
        }

        function copyAllSet() {
            if (envEditorAllVariables.length === 0) {
                window.copyToast('No set values to copy');
                return;
            }
            const text = envEditorAllVariables.map(v => v.key + '=' + v.value).join('\n');
            navigator.clipboard.writeText(text).then(() => {
                window.copyToast('Copied ' + envEditorAllVariables.length + ' variable(s)');
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                window.copyToast('Copied ' + envEditorAllVariables.length + ' variable(s)');
            });
        }
    </script>
</x-layouts.app>
