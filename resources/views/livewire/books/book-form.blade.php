<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-50 via-gray-50 to-slate-50 border-b border-gray-200 sticky top-0 z-10 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <button wire:click="cancel" class="group flex items-center text-gray-600 hover:text-gray-900 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back</span>
                    </button>
                    <div class="border-l border-gray-300 dark:border-gray-600 pl-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $this->pageTitle }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between mb-8">
            @foreach(range(1, $totalSteps) as $step)
                <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                    <button wire:click="goToStep({{ $step }})" 
                            class="flex items-center justify-center w-10 h-10 rounded-full font-bold transition-all
                                {{ $currentStep === $step ? 'bg-blue-600 text-white' : ($currentStep > $step ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600') }}">
                        {{ $step }}
                    </button>
                    @if($step < $totalSteps)
                        <div class="flex-1 h-1 mx-2 {{ $currentStep > $step ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Notifications -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
        @endif

        <!-- Form Container -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <form wire:submit.prevent="submit" class="p-6">
                <!-- Step 1: Basic Information -->
                @if($currentStep === 1)
                    @include('livewire.books.steps.basic-info')
                @endif

                <!-- Step 2: Book Details -->
                @if($currentStep === 2)
                    @include('livewire.books.steps.book-details')
                @endif

                <!-- Step 3: Table of Contents -->
                @if($currentStep === 3)
                    @include('livewire.books.steps.table-of-contents')
                @endif

                <!-- Step 4: Media Files -->
                @if($currentStep === 4)
                    @include('livewire.books.steps.media-files')
                @endif

                <!-- Step 5: Publishing Settings -->
                @if($currentStep === 5)
                    @include('livewire.books.steps.publishing-settings')
                @endif

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-8">
                    <button type="button" wire:click="previousStep" 
                            @if($currentStep === 1) disabled @endif
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    
                    @if($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-blue-700 hover:to-indigo-700 shadow-lg">
                            Next Step
                        </button>
                    @else
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 border border-transparent rounded-xl text-sm font-medium text-white hover:from-green-700 hover:to-emerald-700 shadow-lg">
                            {{ $this->submitButtonText }}
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
