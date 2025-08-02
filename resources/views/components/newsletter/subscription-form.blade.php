@props([
    'theme' => 'light',
    'size' => 'default',
    'showName' => false,
    'buttonText' => 'Subscribe'
])

@php
    $themeClasses = match($theme) {
        'dark' => 'bg-gray-800 text-white',
        'primary' => 'bg-primary-50 text-primary-900',
        default => 'bg-white text-gray-900'
    };

    $sizeClasses = match($size) {
        'compact' => 'p-4',
        'large' => 'p-8',
        default => 'p-6'
    };
@endphp

<div class="newsletter-subscription {{ $themeClasses }} {{ $sizeClasses }} rounded-lg shadow-sm border"
     x-data="newsletterForm()"
     x-init="init()">

    <div class="text-center mb-4">
        <h3 class="text-lg font-semibold mb-2">
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Stay Updated
        </h3>
        <p class="text-sm {{ $theme === 'dark' ? 'text-gray-300' : 'text-gray-600' }}">
            Get the latest educational content and updates delivered to your inbox.
        </p>
    </div>

    <!-- Success Message -->
    <div x-show="success"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-800 text-sm font-medium">Successfully subscribed! Check your email for confirmation.</p>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="error"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-800 text-sm" x-text="errorMessage"></p>
    </div>

    <form @submit.prevent="subscribe()" x-show="!success">
        <div class="space-y-3">
            @if($showName)
                <div>
                    <input type="text"
                           x-model="form.name"
                           placeholder="Your name (optional)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>
            @endif

            <div>
                <input type="email"
                       x-model="form.email"
                       placeholder="Enter your email address"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            </div>

            <button type="submit"
                    :disabled="loading"
                    class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-sm flex items-center justify-center">
                <span x-show="!loading">{{ $buttonText }}</span>
                <span x-show="loading" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Subscribing...
                </span>
            </button>
        </div>

        <p class="text-xs {{ $theme === 'dark' ? 'text-gray-400' : 'text-gray-500' }} mt-3 text-center">
            We respect your privacy. Unsubscribe at any time.
        </p>
    </form>
</div>

<script>
    function newsletterForm() {
        return {
            form: {
                email: '',
                name: ''
            },
            loading: false,
            success: false,
            error: false,
            errorMessage: '',

            init() {
                // Initialize if needed
            },

            async subscribe() {
                this.loading = true;
                this.error = false;
                this.errorMessage = '';

                try {
                    const response = await fetch('{{ route("newsletter.subscribe") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.success = true;
                        this.form = { email: '', name: '' };
                    } else {
                        this.error = true;
                        this.errorMessage = data.message || 'An error occurred. Please try again.';
                    }
                } catch (error) {
                    this.error = true;
                    this.errorMessage = 'Network error. Please check your connection and try again.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
