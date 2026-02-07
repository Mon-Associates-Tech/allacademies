<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-6 flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if (session()->has('info'))
            <div class="mb-6 flex items-center gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 px-4 py-3 rounded-xl">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Student ID Card Settings</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Configure the ID card template and customize field labels for your school.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Template Selection --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Choose Template</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($availableTemplates as $templateKey => $template)
                            <div wire:click="selectTemplate('{{ $templateKey }}')" class="relative cursor-pointer group">
                                <div class="rounded-xl border-2 p-3 transition-all {{ $selectedTemplate === $templateKey ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                                    {{-- Template Preview --}}
                                    @if($templateKey === 'professional')
                                        <div class="aspect-[1.586/1] rounded-lg overflow-hidden mb-3 bg-white border border-gray-200">
                                            <div class="h-1 bg-gradient-to-r from-blue-600 to-blue-400"></div>
                                            <div class="p-2 bg-gray-50 border-b border-gray-100 flex items-center gap-1.5">
                                                <div class="w-4 h-4 rounded bg-gray-200"></div>
                                                <div class="flex-1"><div class="h-1.5 bg-gray-300 rounded w-3/4"></div></div>
                                            </div>
                                            <div class="p-2 flex gap-2">
                                                <div class="w-6 h-8 bg-gray-100 rounded border border-gray-200"></div>
                                                <div class="flex-1 space-y-1"><div class="h-2 bg-gray-800 rounded w-2/3"></div><div class="h-1.5 bg-blue-500 rounded w-1/2"></div><div class="h-1 bg-gray-200 rounded w-full"></div></div>
                                            </div>
                                        </div>
                                    @elseif($templateKey === 'modern')
                                        <div class="aspect-[1.586/1] rounded-lg overflow-hidden mb-3 bg-gradient-to-br from-slate-800 to-slate-900">
                                            <div class="h-full p-2 flex flex-col">
                                                <div class="flex items-center gap-1.5 mb-1"><div class="w-3 h-3 rounded bg-white/20"></div><div class="h-1.5 bg-white/60 rounded w-1/3"></div></div>
                                                <div class="flex-1 flex gap-2">
                                                    <div class="w-5 h-7 bg-white/10 rounded"></div>
                                                    <div class="flex-1 space-y-1"><div class="h-2 bg-white rounded w-2/3"></div><div class="h-1.5 bg-blue-400 rounded w-1/2"></div></div>
                                                    <div class="w-5 h-5 bg-white rounded"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="aspect-[1.586/1] rounded-lg overflow-hidden mb-3 bg-stone-100 border border-stone-300">
                                            <div class="h-full p-2 flex flex-col">
                                                <div class="flex items-center justify-center gap-1.5 pb-1 border-b border-stone-200"><div class="w-4 h-4 rounded-full bg-stone-400"></div><div class="h-1.5 bg-stone-600 rounded w-1/3"></div></div>
                                                <div class="flex-1 flex gap-2 pt-1">
                                                    <div class="w-5 h-7 bg-white border border-stone-300"></div>
                                                    <div class="flex-1 space-y-1"><div class="h-2 bg-stone-700 rounded w-2/3"></div><div class="h-1 bg-stone-400 rounded w-full"></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $template['name'] }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $template['description'] }}</p>
                                    @if($selectedTemplate === $templateKey)
                                        <div class="absolute top-2 right-2"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Field Labels --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Customize Labels</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Customize the labels that appear on the ID card.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($fieldLabels as $field => $label)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                <input type="text" wire:model="fieldLabels.{{ $field }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="{{ ucwords(str_replace('_', ' ', $field)) }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Optional Fields --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Optional Fields</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Select which optional fields to display on the ID card.</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($optionalFields as $fieldKey => $fieldInfo)
                            <label class="flex items-center gap-2 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ in_array($fieldKey, $enabledOptionalFields) ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800' : '' }}">
                                <input type="checkbox" wire:click="toggleOptionalField('{{ $fieldKey }}')" {{ in_array($fieldKey, $enabledOptionalFields) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $fieldInfo['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column - Preview --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Preview</h2>
                    {{-- Preview Card --}}
                    <div class="mb-6">
                        @if($selectedTemplate === 'professional')
                            <div class="aspect-[1.586/1] rounded-lg overflow-hidden bg-white border border-gray-200 shadow-sm">
                                <div class="h-1.5 bg-gradient-to-r from-blue-600 to-blue-400"></div>
                                <div class="p-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-xs text-gray-400">Logo</div>
                                    <div><div class="text-xs font-semibold text-gray-800">{{ $school->name ?? 'School Name' }}</div><div class="text-[8px] text-gray-500 uppercase">Student ID Card</div></div>
                                    <div class="ml-auto px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[7px] font-medium rounded">Active</div>
                                </div>
                                <div class="p-3 flex gap-3">
                                    <div class="w-12 h-14 bg-gray-100 rounded border border-gray-200 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg></div>
                                    <div class="flex-1"><div class="text-sm font-bold text-gray-900">Student Name</div><div class="text-[10px] text-blue-600 font-mono">STU2024001</div><div class="mt-1 grid grid-cols-2 gap-1"><div><div class="text-[7px] text-gray-400 uppercase">Class</div><div class="text-[9px] text-gray-700">Grade 10</div></div><div><div class="text-[7px] text-gray-400 uppercase">Section</div><div class="text-[9px] text-gray-700">A</div></div></div></div>
                                </div>
                            </div>
                        @elseif($selectedTemplate === 'modern')
                            <div class="aspect-[1.586/1] rounded-lg overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 text-white shadow-sm">
                                <div class="h-full p-3 flex flex-col">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2"><div class="w-6 h-6 rounded-lg bg-white/10"></div><div><div class="text-[9px] font-semibold">{{ $school->name ?? 'School Name' }}</div><div class="text-[6px] text-white/50 uppercase">Student ID</div></div></div>
                                        <div class="flex items-center gap-1 px-1.5 py-0.5 bg-emerald-500/20 text-emerald-400 text-[6px] font-medium rounded border border-emerald-500/30"><span class="w-1 h-1 rounded-full bg-emerald-400"></span>Active</div>
                                    </div>
                                    <div class="flex-1 flex gap-2">
                                        <div class="w-10 h-12 bg-white/10 rounded flex items-center justify-center"><svg class="w-5 h-5 text-white/30" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg></div>
                                        <div class="flex-1"><div class="text-xs font-bold">Student Name</div><div class="text-[9px] text-blue-400 font-mono">STU2024001</div><div class="mt-1 grid grid-cols-2 gap-1"><div><div class="text-[6px] text-white/40 uppercase">Class</div><div class="text-[8px]">Grade 10</div></div><div><div class="text-[6px] text-white/40 uppercase">Section</div><div class="text-[8px]">A</div></div></div></div>
                                        <div class="w-8 h-8 bg-white rounded p-0.5"><div class="w-full h-full bg-[repeating-conic-gradient(#1e293b_0%_25%,#fff_0%_50%)] bg-[length:3px_3px]"></div></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="aspect-[1.586/1] rounded-lg overflow-hidden bg-stone-100 border border-stone-300 shadow-sm">
                                <div class="h-full p-3 flex flex-col">
                                    <div class="flex items-center justify-center gap-2 pb-2 border-b border-stone-200">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-stone-500 to-stone-600 flex items-center justify-center text-[6px] text-white font-bold">Crest</div>
                                        <div class="text-center"><div class="text-[10px] font-bold text-stone-800 uppercase">{{ $school->name ?? 'School Name' }}</div><div class="text-[7px] text-stone-500 italic">— Student Identity Card —</div></div>
                                    </div>
                                    <div class="flex-1 flex gap-2 pt-2">
                                        <div class="w-10 h-12 bg-white border border-stone-300 flex items-center justify-center"><svg class="w-5 h-5 text-stone-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg></div>
                                        <div class="flex-1"><div class="text-xs font-bold text-stone-800 font-serif">Student Name</div><div class="mt-1 space-y-0.5"><div class="flex text-[8px]"><span class="text-stone-500 w-10">Reg. No.</span><span class="text-stone-700">STU2024001</span></div><div class="flex text-[8px]"><span class="text-stone-500 w-10">Class</span><span class="text-stone-700">Grade 10</span></div></div></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Required Fields --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Required Fields</h3>
                        <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                            @foreach($requiredFields as $fieldKey => $fieldInfo)
                                <li class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ $fieldInfo['label'] }}</li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-3">
                        <button wire:click="saveSettings" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Settings
                        </button>
                        <button wire:click="resetToDefaults" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reset to Defaults
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
