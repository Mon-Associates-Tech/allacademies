<x-exam.page>
    <!-- Header with Back Button and Title -->
    <div class="mb-8">
        <button wire:click="cancel" class="group flex items-center gap-1.5 text-sm font-medium text-muted hover:text-ink transition-colors mb-6">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to books
        </button>

        <div class="flex items-start justify-between gap-6">
            <div>
                <h1 class="font-display text-2xl font-bold text-ink tracking-tight">{{ $this->pageTitle }}</h1>
                <p class="mt-1 text-sm text-muted">Complete each step to publish your book</p>
            </div>

            <!-- Step Progress Pill -->
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-xs font-medium text-muted uppercase tracking-wider">Progress</span>
                <div class="flex items-center gap-2">
                    <div class="w-24 h-2 bg-line rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-500"
                             style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-ink tabular-nums">{{ $currentStep }}/{{ $totalSteps }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step Indicators -->
    <div class="mb-8">
        <div class="flex items-center gap-0">
            @php
                $stepLabels = ['Basic Info', 'Details', 'Contents', 'Media', 'Publish'];
            @endphp
            @foreach(range(1, $totalSteps) as $step)
                @php
                    $active = $currentStep === $step;
                    $completed = $currentStep > $step;
                    $pending = $currentStep < $step;
                @endphp
                <button wire:click="goToStep({{ $step }})"
                        class="group flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-all border-b-2
                            {{ $active ? 'border-primary text-primary' : '' }}
                            {{ $completed ? 'border-ok text-ok hover:text-ok/80' : '' }}
                            {{ $pending ? 'border-transparent text-muted hover:text-ink' : '' }}">
                    @if($currentStep > $step)
                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-ok text-white text-[10px]">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    @else
                        <span class="flex items-center justify-center w-5 h-5 rounded-full text-[11px] font-bold
                            {{ $currentStep === $step ? 'bg-primary text-on-primary' : 'bg-line text-muted' }}">
                            {{ $step }}
                        </span>
                    @endif
                    <span>{{ $stepLabels[$step - 1] ?? '' }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Notifications -->
    @if (session()->has('message'))
        <x-exam.flash type="success" :message="session('message')" class="mb-6" />
    @endif

    <!-- Form Container -->
    <form wire:submit.prevent="submit">
        <x-exam.card :padded="false">
            <div class="p-6">
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
            </div>

            <x-slot:footer>
                <x-exam.savebar :hint="sprintf('Step %d of %d', $currentStep, $totalSteps)">
                    <div class="flex items-center gap-3">
                        <x-exam.button type="button" variant="ghost" wire:click="previousStep" :disabled="$currentStep === 1">
                            <span class="inline-flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>Previous</span>
                        </x-exam.button>

                        @if($currentStep < $totalSteps)
                            <x-exam.button type="button" wire:click="nextStep" target="nextStep">
                                <span class="inline-flex items-center">Next Step<svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
                            </x-exam.button>
                        @else
                            <button type="submit" class="inline-flex items-center rounded-md bg-ok px-4 py-2 text-sm font-medium text-on-ok transition-colors hover:bg-ok/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-primary">
                                {{ $this->submitButtonText }}
                            </button>
                        @endif
                    </div>
                </x-exam.savebar>
            </x-slot:footer>
        </x-exam.card>
    </form>
</x-exam.page>
