
<!-- Multi-step Progress Indicator -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        @foreach(['Personal Info', 'Preferences', 'Confirmation'] as $index => $step)
            <div class="flex items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-200
                           {{ $currentStep > $index + 1 ? 'bg-green-500 border-green-500 text-white' : 
                              ($currentStep == $index + 1 ? 'bg-blue-500 border-blue-500 text-white' : 
                               'border-gray-300 text-gray-500') }}">
                    @if($currentStep > $index + 1)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        {{ $index + 1 }}
                    @endif
                </div>
                
                <div class="ml-3">
                    <p class="text-sm font-medium {{ $currentStep >= $index + 1 ? 'text-gray-900' : 'text-gray-500' }}">
                        {{ $step }}
                    </p>
                </div>
                
                @if($index < count($steps) - 1)
                    <div class="flex-1 ml-4 mr-4">
                        <div class="h-0.5 {{ $currentStep > $index + 1 ? 'bg-green-500' : 'bg-gray-200' }} transition-colors duration-200"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
