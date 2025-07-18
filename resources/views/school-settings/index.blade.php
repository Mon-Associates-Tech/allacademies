<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    School Settings
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your school's configuration and preferences
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <button type="button"
                        onclick="showCreateModal()"
                        class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Add Setting
                </button>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Settings Groups -->
        <div class="space-y-8">
            @forelse($settings as $group => $groupSettings)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 capitalize">
                            {{ str_replace('_', ' ', $group) }}
                        </h3>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl class="divide-y divide-gray-200">
                            @foreach($groupSettings as $setting)
                                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-900">
                                        {{ $setting->label }}
                                        @if($setting->required)
                                            <span class="text-red-500">*</span>
                                        @endif
                                        @if($setting->description)
                                            <p class="mt-1 text-sm text-gray-500">{{ $setting->description }}</p>
                                        @endif
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 mr-4">
                                                @include('school-settings.partials.value-display', ['setting' => $setting])
                                            </div>
                                            <div class="flex space-x-2">
                                                <button type="button"
                                                        onclick="editValue({{ json_encode($setting) }})"
                                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                        onclick="editSetting({{ json_encode($setting) }})"
                                                        class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                                    Configure
                                                </button>
                                                <button type="button"
                                                        onclick="deleteSetting('{{ $setting->id }}')"
                                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                                <!-- Edit Value Modal -->
                                @include('school-settings.partials.value-modal', ['setting' => $setting])
                            @endforeach
                        </dl>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No settings configured</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first school setting.</p>
                    <div class="mt-6">
                        <button type="button"
                                onclick="showCreateModal()"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            Add Setting
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create/Edit Setting Modal -->
    @include('school-settings.partials.settings-modal')



    <!-- Delete Confirmation Modal -->
    @include('school-settings.partials.delete-modal')


    @push('scripts')
        <script>
            let currentSettingId = null;
            let currentSetting = null;

            function showCreateModal() {
                currentSettingId = null;
                currentSetting = null;
                document.getElementById('settingModalTitle').textContent = 'Add New Setting';
                document.getElementById('settingForm').reset();
                document.getElementById('settingForm').action = '{{ route('school-settings.store') }}';
                document.getElementById('settingModal').classList.remove('hidden');
                toggleOptionsDiv();
            }

            function editSetting(setting) {
                currentSettingId = setting.id;
                currentSetting = setting;
                document.getElementById('settingModalTitle').textContent = 'Edit Setting';

                // Populate form fields
                document.getElementById('key').value = setting.key;
                document.getElementById('type').value = setting.type;
                document.getElementById('label').value = setting.label;
                document.getElementById('description').value = setting.description || '';
                document.getElementById('group').value = setting.group;
                document.getElementById('sort_order').value = setting.sort_order;
                document.getElementById('required').checked = setting.required;

                if (setting.options) {
                    document.getElementById('options').value = JSON.stringify(setting.options);
                }

                // Update form action
                document.getElementById('settingForm').action = `/school-settings/${setting.id}`;

                // Add method input for PUT
                let methodInput = document.getElementById('methodInput');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    methodInput.id = 'methodInput';
                    document.getElementById('settingForm').appendChild(methodInput);
                }

                document.getElementById('settingModal').classList.remove('hidden');
                toggleOptionsDiv();
            }

            function editValue(setting) {
                currentSettingId = setting.id;
                currentSetting = setting;

                // Set form action
                document.getElementById('valueForm').action = `/school-settings/${setting.id}/value`;

                // Generate input based on type
                const inputHtml = generateValueInput(setting);
                document.getElementById('valueInput').innerHTML = inputHtml;

                document.getElementById('valueModal').classList.remove('hidden');
            }

            function generateValueInput(setting) {
                let html = `<label class="block text-sm font-medium text-gray-700 mb-2">${setting.label}</label>`;

                switch (setting.type) {
                    case 'text':
                        html += `<input type="text" name="value" value="${setting.raw_value || ''}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" ${setting.required ? 'required' : ''}>`;
                        break;
                    case 'longtext':
                        html += `<textarea name="value" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" ${setting.required ? 'required' : ''}>${setting.raw_value || ''}</textarea>`;
                        break;
                    case 'number':
                        html += `<input type="number" name="value" value="${setting.raw_value || ''}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" ${setting.required ? 'required' : ''}>`;
                        break;
                    case 'boolean':
                        html += `<select name="value" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="1" ${setting.raw_value === '1' ? 'selected' : ''}>Yes</option>
                    <option value="0" ${setting.raw_value === '0' ? 'selected' : ''}>No</option>
                </select>`;
                        break;
                    case 'select':
                        html += `<select name="value" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" ${setting.required ? 'required' : ''}>`;
                        if (!setting.required) {
                            html += `<option value="">Select an option</option>`;
                        }
                        if (setting.options) {
                            setting.options.forEach(option => {
                                html += `<option value="${option}" ${setting.raw_value === option ? 'selected' : ''}>${option}</option>`;
                            });
                        }
                        html += `</select>`;
                        break;
                    case 'image':
                        html += `<input type="file" name="value" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">`;
                        if (setting.value) {
                            html += `<div class="mt-2"><img src="${setting.value}" alt="Current image" class="h-20 w-20 object-cover rounded"></div>`;
                        }
                        break;
                    case 'pdf':
                        html += `<input type="file" name="value" accept=".pdf" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">`;
                        if (setting.value) {
                            html += `<div class="mt-2"><a href="${setting.value}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View current PDF</a></div>`;
                        }
                        break;
                    case 'json':
                        html += `<textarea name="value" rows="6" placeholder='{"key": "value"}' class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono text-sm" ${setting.required ? 'required' : ''}>${setting.raw_value || ''}</textarea>`;
                        break;
                    default:
                        html += `<input type="text" name="value" value="${setting.raw_value || ''}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" ${setting.required ? 'required' : ''}>`;
                }

                return html;
            }

            function deleteSetting(id) {
                currentSettingId = id;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');

                // Reset form if closing setting modal
                if (modalId === 'settingModal') {
                    document.getElementById('settingForm').reset();
                    document.getElementById('settingForm').action = '{{ route('school-settings.store') }}';

                    // Remove method input
                    const methodInput = document.getElementById('methodInput');
                    if (methodInput) {
                        methodInput.remove();
                    }
                }
            }

            function confirmDelete() {
                if (currentSettingId) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/school-settings/${currentSettingId}`;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = '{{ csrf_token() }}';

                    form.appendChild(methodInput);
                    form.appendChild(tokenInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function toggleOptionsDiv() {
                const typeSelect = document.getElementById('type');
                const optionsDiv = document.getElementById('optionsDiv');

                if (typeSelect.value === 'select' || typeSelect.value === 'radio') {
                    optionsDiv.classList.remove('hidden');
                } else {
                    optionsDiv.classList.add('hidden');
                }
            }

            // Type change handler
            document.getElementById('type').addEventListener('change', toggleOptionsDiv);

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const modals = ['settingModal', 'valueModal', 'deleteModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target === modal) {
                        closeModal(modalId);
                    }
                });
            });
        </script>
    @endpush
</x-layouts.app>

