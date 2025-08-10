<!-- Step 5: Publishing Settings -->
<div class="mb-12">
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">5</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Publishing Settings</h3>
            <p class="text-gray-600">Choose the publishing status for your book</p>
        </div>
    </div>

    <div class="ml-14">
        <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-6">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Publishing Status <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-4">
                    Choose whether to publish this book immediately or save it as a draft.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($this->publishingStatusOptions as $value => $label)
                    @php
                        $statusEnum = App\Enums\PublishingStatus::from($value);
                    @endphp
                    <label class="relative flex items-start p-6 bg-white border-2 {{ $status === $value ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200' }} rounded-xl cursor-pointer hover:border-blue-300 hover:shadow-md transition-all">
                        <input type="radio" wire:model="status" value="{{ $value }}"
                               class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 mt-1">
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-2 {{ $value === 'published' ? 'text-green-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusEnum->getIcon() }}"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">{{ $label }}</span>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusEnum->getColorClass() }}">
                                    {{ $label }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $statusEnum->getDescription() }}</p>

                            @if($value === 'published')
                                <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center text-sm text-green-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Book will be {{ $mode === 'edit' ? 'updated and remain' : 'immediately' }} available to all users
                                    </div>
                                </div>
                            @else
                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-center text-sm text-yellow-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        Book will be saved but not visible to users until published
                                    </div>
                                </div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
            @error('status') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror

            <!-- Current Status Display -->
            <div class="mt-6 p-4 bg-white border border-gray-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-700">Current Status:</span>
                        <span class="ml-2 px-3 py-1 text-sm font-medium rounded-full {{ $this->currentPublishingStatus->getColorClass() }}">
                            {{ $this->currentPublishingStatus->getLabel() }}
                        </span>
                    </div>
                    @if($this->currentPublishingStatus->value === 'published')
                        <div class="flex items-center text-sm text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $mode === 'edit' ? 'Will remain published' : 'Will be published immediately' }}
                        </div>
                    @else
                        <div class="flex items-center text-sm text-yellow-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Can be published later
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
