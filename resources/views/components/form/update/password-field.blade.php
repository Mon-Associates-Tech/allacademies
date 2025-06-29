@props([
    'label' => 'Password',
    'model' => 'password',
    'placeholder' => 'Enter password',
    'showStrength' => false,
    'required' => true
])

<div class="space-y-2" x-data="{ show: false }">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            wire:model.live="{{ $model }}"
            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                   dark:bg-gray-700 dark:text-white transition-colors duration-200
                   @error($model) border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
            placeholder="{{ $placeholder }}"
        >

        <button
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center hover:text-gray-600"
        >
            <svg x-show="!show" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
            </svg>
            <svg x-show="show" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    <!-- Password Strength Indicator -->
    @if($showStrength && $model === 'password')
        <div class="mt-2" x-show="$wire.{{ $model }}.length > 0">
            <div class="flex justify-between items-center mb-1">
                <span class="text-xs text-gray-500">Password Strength</span>
                <span class="text-xs font-medium"
                      x-data="{ strength: $wire.getPasswordStrength() }"
                      :class="{
                          'text-red-500': strength < 2,
                          'text-yellow-500': strength >= 2 && strength < 4,
                          'text-green-500': strength >= 4
                      }">
                    {{ $this->getPasswordStrengthText() }}
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300"
                     x-data="{ strength: $wire.getPasswordStrength() }"
                     :class="{
                         'bg-red-500': strength < 2,
                         'bg-yellow-500': strength >= 2 && strength < 4,
                         'bg-green-500': strength >= 4
                     }"
                     :style="`width: ${Math.min(strength * 25, 100)}%`"></div>
            </div>
        </div>
    @endif

    @error($model)
    <p class="text-red-500 text-sm flex items-center mt-1">
        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        {{ $message }}
    </p>
    @enderror
</div>
