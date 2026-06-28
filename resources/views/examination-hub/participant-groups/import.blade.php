<x-layouts.app>
    <x-examination-hub.navigation active="participant-groups"/>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── BACK LINK ── --}}
        <a href="{{ route('examination-hub.participant-groups.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-900 dark:hover:text-amber-300 transition-colors uppercase tracking-wider"
           style="letter-spacing: 0.08em;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Groups
        </a>

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #4c1d95, #7c3aed, #a78bfa);"></div>
            <div class="px-7 py-6">
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Import Participants from CSV
                </h1>
                <p class="text-sm text-slate-400 mt-1">Upload a CSV file to import participants into groups</p>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-emerald-700 dark:text-emerald-400 font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #991b1b, #dc2626, #f87171);"></div>
                <div class="bg-white dark:bg-slate-900 px-5 py-4 text-sm text-red-700 dark:text-red-400 font-medium flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ── TWO COLUMN LAYOUT ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Upload Form --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #1d4ed8, #3b82f6); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Upload CSV File</h2>
                </div>
                <form action="{{ route('examination-hub.participant-groups.import.process') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    
                    <div>
                        <label for="csv_file" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">
                            CSV File <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 p-8 text-center hover:border-amber-500 dark:hover:border-amber-500 transition-colors" style="border-radius: 2px;">
                            <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt,.xlsx,.xls" class="hidden" required onchange="updateFileName(this)">
                            <label for="csv_file" class="cursor-pointer block">
                                <svg class="w-12 h-12 mx-auto text-slate-400 dark:text-slate-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Click to upload or drag and drop</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">CSV files only (max 20MB)</p>
                            </label>
                        </div>
                        <p id="fileName" class="text-xs font-medium text-slate-600 dark:text-slate-400 mt-2"></p>
                    </div>

                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-white uppercase tracking-wider transition-opacity hover:opacity-90"
                            style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); border-radius: 2px; letter-spacing: 0.08em;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import Participants
                    </button>
                </form>
            </div>

            {{-- Instructions --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #b45309, #fbbf24); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">CSV Format Instructions</h2>
                </div>
                <div class="p-6 space-y-6">
                    
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">Required Columns</h3>
                        <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                            <li class="flex items-start gap-2">
                                <span class="inline-flex items-center justify-center text-[10px] font-mono font-bold px-2 py-0.5 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300 mt-0.5" style="border-radius: 2px;">name</span>
                                <span>Participant's full name</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="inline-flex items-center justify-center text-[10px] font-mono font-bold px-2 py-0.5 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300 mt-0.5" style="border-radius: 2px;">email</span>
                                <span>Participant's email address</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="inline-flex items-center justify-center text-[10px] font-mono font-bold px-2 py-0.5 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300 mt-0.5" style="border-radius: 2px;">group</span>
                                <span>Group name (will be created if doesn't exist)</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3" style="letter-spacing: 0.08em;">Optional Columns</h3>
                        <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                            <li class="flex items-start gap-2">
                                <span class="inline-flex items-center justify-center text-[10px] font-mono font-bold px-2 py-0.5 border border-slate-200 dark:border-slate-600 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-700 dark:to-slate-800 text-slate-700 dark:text-slate-300 mt-0.5" style="border-radius: 2px;">unique_code</span>
                                <span>Unique identifier for participant</span>
                            </li>
                        </ul>
                    </div>

                    <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                        <div class="h-1 w-full" style="background: linear-gradient(90deg, #1d4ed8, #3b82f6);"></div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4">
                            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Example CSV Format</h3>
                            <pre class="bg-white dark:bg-slate-900 p-3 text-xs font-mono text-slate-700 dark:text-slate-300 overflow-x-auto border border-slate-200 dark:border-slate-700" style="border-radius: 2px;">name,email,group,unique_code
John Doe,john@example.com,Group A,CODE123
Jane Smith,jane@example.com,Group A,CODE456
Bob Wilson,bob@example.com,Group B,CODE789</pre>
                        </div>
                    </div>

                    <div class="overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                        <div class="h-1 w-full" style="background: linear-gradient(90deg, #b45309, #d97706, #fbbf24);"></div>
                        <div class="bg-white dark:bg-slate-900 px-5 py-4">
                            <h3 class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider mb-2 flex items-center gap-2" style="letter-spacing: 0.08em;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Important Notes
                            </h3>
                            <ul class="list-disc list-inside text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                <li>Groups will be automatically created if they don't exist</li>
                                <li>Duplicate emails within the same group will be updated</li>
                                <li>Each participant can belong to multiple groups</li>
                                <li>Invalid rows will be skipped with error messages</li>
                            </ul>
                        </div>
                    </div>

                    <a href="data:text/csv;charset=utf-8,name%2Cemail%2Cgroup%2Cunique_code%0AJohn%20Doe%2Cjohn%40example.com%2CGroup%20A%2CCODE123%0AJane%20Smith%2Cjane%40example.com%2CGroup%20A%2CCODE456%0ABob%20Wilson%2Cbob%40example.com%2CGroup%20B%2CCODE789"
                       download="sample_participants.csv"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 transition-colors hover:bg-slate-50 dark:hover:bg-slate-700 uppercase tracking-wider"
                       style="border-radius: 2px; letter-spacing: 0.08em;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Sample CSV
                    </a>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function updateFileName(input) {
                const fileName = document.getElementById('fileName');
                if (input.files && input.files[0]) {
                    fileName.textContent = 'Selected: ' + input.files[0].name;
                } else {
                    fileName.textContent = '';
                }
            }
        </script>
    @endpush
</x-layouts.app>