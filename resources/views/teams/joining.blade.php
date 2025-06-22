<x-layouts.app title="Join Team" :has-action="false" title-align-center="true">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <!-- Team icon -->
                <div class="mx-auto h-12 w-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">Join a Team</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter the 8-character code shared by your team owner to join their team
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10">
                <form method="POST" action="{{ route('teams.add-member') }}" class="space-y-6">
                    @csrf

                    <!-- Code Input -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Team Code
                        </label>
                        <div class="relative">
                            <x-form.input
                                name="code"
                                type="text"
                                class="text-center text-lg font-mono uppercase tracking-widest"
                                placeholder="ABC12345"
                                maxlength="8"
                                autocomplete="off"
                                required
                            />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2m6 0V7a2 2 0 00-2-2H9a2 2 0 00-2-2v2m6 0h.01M9 7h.01" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Code should be exactly 8 characters long
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <x-button.primary type="submit" class="w-full justify-center">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Join Team
                        </x-button.primary>
                    </div>
                </form>

                <!-- Additional Help -->
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Don't have a team code?
                        </p>
                        <a href="{{ route('teams.create') }}" class="mt-2 inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-500">
                            Create your own team
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript for better UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.querySelector('input[name="code"]');

            if (codeInput) {
                // Auto-format input to uppercase
                codeInput.addEventListener('input', function(e) {
                    e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                });

                // Auto-focus on page load
                codeInput.focus();

                // Add visual feedback for valid length
                codeInput.addEventListener('input', function(e) {
                    const value = e.target.value;
                    if (value.length === 8) {
                        e.target.classList.add('border-green-500', 'ring-green-500');
                        e.target.classList.remove('border-red-500', 'ring-red-500');
                    } else if (value.length > 0) {
                        e.target.classList.add('border-red-500', 'ring-red-500');
                        e.target.classList.remove('border-green-500', 'ring-green-500');
                    } else {
                        e.target.classList.remove('border-green-500', 'ring-green-500', 'border-red-500', 'ring-red-500');
                    }
                });
            }
        });
    </script>
</x-layouts.app>
