<div>
    <x-exam.callout tone="primary" class="mb-8">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 bg-primary text-on-primary rounded-full text-sm font-bold shrink-0">2</span>
            <div>
                <h3 class="font-bold text-ink">Book Details</h3>
                <p class="text-sm text-ink-soft">Provide additional information about the book</p>
            </div>
        </div>
    </x-exam.callout>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
        <!-- Edition -->
        <x-exam.field label="Edition">
            <x-exam.input type="text" wire:model="edition" placeholder="e.g., 1st Edition, Revised Edition" error-key="edition" />
        </x-exam.field>

        <!-- Publisher -->
        <x-exam.field label="Publisher">
            <x-exam.input type="text" wire:model="publisher" placeholder="Publishing house name" error-key="publisher" />
        </x-exam.field>

        <!-- Pages -->
        <x-exam.field label="Number of Pages">
            <x-exam.input type="number" wire:model.live="pages" min="1" placeholder="Total pages" error-key="pages" />
        </x-exam.field>

        <!-- Subscription Fee -->
        <x-exam.field label="Annual Subscription Fee" tag="GHS">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <span class="text-muted">₵</span>
                </div>
                <x-exam.input type="number" wire:model="annualSubscriptionFee" step="0.01" min="0" placeholder="0.00" error-key="annualSubscriptionFee"
                               class="pl-8" />
            </div>
            <p class="mt-1 font-mono text-[11px] text-muted">Enter 0 to make this book free</p>
        </x-exam.field>

        <!-- Book Formats -->
        <x-exam.field label="Available Formats" class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="relative flex items-start p-4 rounded-md border border-line-strong cursor-pointer hover:border-primary hover:bg-primary/5 transition-all">
                    <input type="checkbox" wire:model="hasHardcopy"
                           class="h-4 w-4 text-primary focus:ring-primary border-line-strong rounded mt-0.5">
                    <div class="ml-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="font-semibold text-sm text-ink">Physical Hardcopy</span>
                        </div>
                        <p class="text-xs text-ink-soft">Traditional printed book available for borrowing</p>
                    </div>
                </label>

                <label class="relative flex items-start p-4 rounded-md border border-line-strong cursor-pointer hover:border-primary hover:bg-primary/5 transition-all">
                    <input type="checkbox" wire:model="hasSoftcopy"
                           class="h-4 w-4 text-primary focus:ring-primary border-line-strong rounded mt-0.5">
                    <div class="ml-3">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-semibold text-sm text-ink">Digital Softcopy</span>
                        </div>
                        <p class="text-xs text-ink-soft">PDF version for online reading and downloads</p>
                    </div>
                </label>
            </div>
            @error('hasHardcopy') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror
        </x-exam.field>

        <!-- Additional Info -->
        <x-exam.field label="Additional Information" class="lg:col-span-2">
            <x-exam.input type="text" wire:model="additionalInfo" placeholder="Any additional information about the book..." error-key="additionalInfo" />
        </x-exam.field>
    </div>
</div>
