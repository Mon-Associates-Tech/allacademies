<div x-show="activeTab === 'system-settings'" class="space-y-6 animate-fade-in">

{{--    @livewire('school-settings.index')--}}

    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            System Settings
        </h3>
        <a href="{{ route('school-settings.index') }}"
           class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Manage Settings
        </a>
    </div>

    @if(count($settingGroups) > 0)
        @foreach($settingGroups as $groupName => $group)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-6 capitalize flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ str_replace('_', ' ', $groupName) }} Settings
                </h4>

                <div class="space-y-6">
                    @foreach($group as $key => $setting)
                        <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                            <div class="flex-1 mr-6">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">{{ $setting['label'] }}</label>
                                @if($setting['description'])
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $setting['description'] }}</p>
                                @endif
                            </div>

                            <!-- Text Input -->
                            @if($setting['type'] === 'text')
                                <div class="w-64">
                                    <input type="text"
                                           value="{{ $setting['value'] }}"
                                           wire:change="updateSetting('{{ $key }}', $event.target.value, '{{ $groupName }}')"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                </div>
                            @endif

                            <!-- Long Text Input -->
                            @if($setting['type'] === 'longtext')
                                <div class="w-full mt-2">
                                    <textarea
                                        rows="3"
                                        wire:change="updateSetting('{{ $key }}', $event.target.value, '{{ $groupName }}')"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">{{ $setting['value'] }}</textarea>
                                </div>
                            @endif

                            <!-- Number Input -->
                            @if($setting['type'] === 'number')
                                <div class="w-32">
                                    <input type="number"
                                           value="{{ $setting['value'] }}"
                                           wire:change="updateSetting('{{ $key }}', $event.target.value, '{{ $groupName }}')"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                </div>
                            @endif

                            <!-- Select Input -->
                            @if($setting['type'] === 'select' && isset($setting['options']) && is_array($setting['options']))
                                <div class="w-48">
                                    <select wire:change="updateSetting('{{ $key }}', $event.target.value, '{{ $groupName }}')"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                                        @foreach($setting['options'] as $option)
                                            <option value="{{ $option }}" {{ $setting['value'] == $option ? 'selected' : '' }}>
                                                {{ ucfirst($option) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Radio Input -->
                            @if($setting['type'] === 'radio' && isset($setting['options']) && is_array($setting['options']))
                                <div class="flex items-center space-x-4">
                                    @foreach($setting['options'] as $option)
                                        <label class="flex items-center">
                                            <input type="radio"
                                                   name="{{ $key }}"
                                                   value="{{ $option }}"
                                                   {{ $setting['value'] == $option ? 'checked' : '' }}
                                                   wire:change="updateSetting('{{ $key }}', '{{ $option }}', '{{ $groupName }}')"
                                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ ucfirst($option) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Boolean Toggle -->
                            @if($setting['type'] === 'boolean')
                                <div class="flex items-center">
                                    <button wire:click="updateSetting('{{ $key }}', {{ $setting['value'] ? 'false' : 'true' }}, '{{ $groupName }}')"
                                            type="button"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ $setting['value'] ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $setting['value'] ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="ml-3 text-sm text-gray-900 dark:text-white font-medium">{{ $setting['value'] ? 'Enabled' : 'Disabled' }}</span>
                                </div>
                            @endif

                            <!-- JSON Input -->
                            @if($setting['type'] === 'json')
                                <div class="w-full mt-2">
                                    <textarea
                                        rows="5"
                                        wire:change="updateSetting('{{ $key }}', $event.target.value, '{{ $groupName }}')"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white text-sm font-mono">{{ is_array($setting['value']) ? json_encode($setting['value'], JSON_PRETTY_PRINT) : $setting['value'] }}</textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter valid JSON format</p>
                                </div>
                            @endif

                            <!-- Image Upload -->
                            @if($setting['type'] === 'image')
                                <div class="w-full mt-2">
                                    @if($setting['value'])
                                        <div class="mb-2">
                                            <img src="{{ $setting['value'] }}" alt="{{ $setting['label'] }}" class="h-20 w-20 object-cover rounded-lg">
                                        </div>
                                    @endif
                                    <input type="file"
                                           accept="image/*"
                                           wire:change="updateSetting('{{ $key }}', $event.target.files[0], '{{ $groupName }}')"
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-full file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            @endif

                            <!-- PDF Upload -->
                            @if($setting['type'] === 'pdf')
                                <div class="w-full mt-2">
                                    @if($setting['value'])
                                        <div class="mb-2">
                                            <a href="{{ $setting['value'] }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                                View current PDF
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file"
                                           accept=".pdf"
                                           wire:change="updateSetting('{{ $key }}', $event.target.files[0], '{{ $groupName }}')"
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-full file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PDF up to 10MB</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No system settings configured</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure system-wide settings for your school.</p>
                <div class="mt-6">
                    <a href="{{ route('school-settings.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Settings
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
