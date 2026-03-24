<div>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                {{ $offerProject ? 'Edit Sponsorship Project' : 'Create Sponsorship Project' }}
            </h2>

            <form wire:submit.prevent="save">
                <!-- Project Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Type <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="type"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Project Name -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="name"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter project name">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="description" rows="4"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe your project, its goals, and impact..."></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Affected Individuals -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Impact Description
                    </label>
                    <textarea wire:model="affected_individuals" rows="3"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe who will benefit and how (e.g., '50 students will receive...')"></textarea>
                    @error('affected_individuals') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Amount Goal -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Funding Goal (GHS) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" wire:model="amount_goal"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                    @error('amount_goal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Deadline -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Deadline (Optional)
                    </label>
                    <input type="date" wire:model="deadline"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    @error('deadline') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Project Images -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Images (Max 10)
                    </label>
                    <input type="file" wire:model="images" multiple accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('images.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Max 10 images, 10MB each</p>

                    <div wire:loading wire:target="images" class="text-sm text-blue-600 mt-2">Uploading...</div>

                    @if(count($existingImages) > 0)
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            @foreach($existingImages as $index => $image)
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}" class="w-full h-24 object-cover rounded-lg">
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if(count($tempImages) > 0)
                        <div class="grid grid-cols-4 gap-3 mt-3">
                            @foreach($tempImages as $index => $image)
                                <div class="relative">
                                    <img src="{{ $image->temporaryUrl() }}" class="w-full h-24 object-cover rounded-lg">
                                    <button type="button" wire:click="removeTempImage({{ $index }})"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Project Videos -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Videos (Max 2)
                    </label>
                    <input type="file" wire:model="videos" multiple accept="video/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('videos.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Max 2 videos, 200MB each</p>

                    <div wire:loading wire:target="videos" class="text-sm text-blue-600 mt-2">Uploading...</div>

                    @if(count($existingVideos) > 0)
                        <div class="space-y-2 mt-3">
                            @foreach($existingVideos as $index => $video)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ basename($video) }}</span>
                                    <button type="button" wire:click="removeVideo({{ $index }})"
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if(count($tempVideos) > 0)
                        <div class="space-y-2 mt-3">
                            @foreach($tempVideos as $index => $video)
                                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $video->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeTempVideo({{ $index }})"
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Beneficiaries Section -->
                <div class="mb-6 border-t pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Beneficiaries</h3>
                    </div>

                    <!-- Add New Beneficiary Form -->
                    <div
                        class="mb-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-3">Add Beneficiary</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newBeneficiaryName"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                       placeholder="Beneficiary name">
                                @error('newBeneficiaryName') <span
                                    class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                <select wire:model="newBeneficiaryType"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                    @foreach($beneficiaryTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" wire:model="newBeneficiaryEmail"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                       placeholder="email@example.com">
                                @error('newBeneficiaryEmail') <span
                                    class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="tel" wire:model="newBeneficiaryPhone"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                       placeholder="+233 XX XXX XXXX">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea wire:model="newBeneficiaryDescription" rows="2"
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm"
                                          placeholder="Brief description of the beneficiary"></textarea>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" wire:click="addBeneficiary"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                                + Add Beneficiary
                            </button>
                        </div>
                    </div>

                    <!-- List of Added Beneficiaries -->
                    @if(count($beneficiaries) > 0)
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-900 dark:text-white">Added Beneficiaries
                                ({{ count($beneficiaries) }})</h4>
                            @foreach($beneficiaries as $index => $beneficiary)
                                <div
                                    class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h5 class="font-medium text-gray-900 dark:text-white">{{ $beneficiary['name'] }}</h5>
                                                <span
                                                    class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ ucfirst($beneficiary['type']) }}
                                                </span>
                                            </div>
                                            @if($beneficiary['email'])
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    📧 {{ $beneficiary['email'] }}</p>
                                            @endif
                                            @if($beneficiary['phone'])
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    📱 {{ $beneficiary['phone'] }}</p>
                                            @endif
                                            @if($beneficiary['description'])
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $beneficiary['description'] }}</p>
                                            @endif
                                        </div>
                                        <button type="button" wire:click="removeBeneficiary({{ $index }})"
                                                class="ml-3 text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No beneficiaries added yet. Use the
                            form above to add beneficiaries.</p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('benefactors.index') }}"
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $offerProject ? 'Update Project' : 'Create Project' }}</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
