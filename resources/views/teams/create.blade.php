<x-layouts.app title="Create New Team" :has-action="false" title-align-center="true">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="text-center">
                <!-- Team creation icon -->
                <div class="mx-auto h-16 w-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">Create Your Team</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Set up a new team to collaborate with others and share resources
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10">
                <!-- General Error Message -->
                @if($errors->has('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ $errors->first('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('teams.store') }}" class="space-y-6" id="team-creation-form">
                    @csrf

                    <!-- Team Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Team Name <span class="text-red-500">*</span>
                        </label>
                        <x-form.input
                            name="name"
                            type="text"
                            placeholder="Enter your team name"
                            required
                            class="team-name-input {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
                            maxlength="100"
                        />
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Choose a unique and descriptive name for your team (2-100 characters)
                        </p>
                        <div class="mt-1 text-xs text-gray-400">
                            <span id="name-counter">0</span>/100 characters
                        </div>
                    </div>

                    <!-- Team Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Team Description
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm {{ $errors->has('description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '' }}"
                            placeholder="Describe your team's purpose, goals, or focus areas..."
                            maxlength="500"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Optional: Help team members understand what this team is about
                        </p>
                        <div class="mt-1 text-xs text-gray-400">
                            <span id="description-counter">0</span>/500 characters
                        </div>
                    </div>

                    <!-- Team Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Team Type <span class="text-red-500">*</span>
                        </label>
                        @error('type')
                            <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : '' }}">
                                <input type="radio" name="type" value="academic" class="sr-only" {{ old('type', 'academic') === 'academic' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="flex items-center">
                                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <span class="block text-sm font-medium text-gray-900">Academic</span>
                                        </span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">
                                            For study groups, research, and educational projects
                                        </span>
                                    </span>
                                </span>
                                <span class="radio-indicator"></span>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : '' }}">
                                <input type="radio" name="type" value="professional" class="sr-only" {{ old('type') === 'professional' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="flex items-center">
                                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6z" />
                                            </svg>
                                            <span class="block text-sm font-medium text-gray-900">Professional</span>
                                        </span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">
                                            For work projects and professional collaboration
                                        </span>
                                    </span>
                                </span>
                                <span class="radio-indicator"></span>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : '' }}">
                                <input type="radio" name="type" value="personal" class="sr-only" {{ old('type') === 'personal' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="flex items-center">
                                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <span class="block text-sm font-medium text-gray-900">Personal</span>
                                        </span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">
                                            For personal projects and hobby groups
                                        </span>
                                    </span>
                                </span>
                                <span class="radio-indicator"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Privacy Settings <span class="text-red-500">*</span>
                        </label>
                        @error('privacy')
                            <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="space-y-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50 {{ $errors->has('privacy') ? 'border-red-300' : '' }}">
                                <input type="radio" name="privacy" value="private" class="sr-only" {{ old('privacy', 'private') === 'private' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <span class="block text-sm font-medium text-gray-900">Private Team</span>
                                        </span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">
                                            Only members can see team content. Members join by invitation or code.
                                        </span>
                                    </span>
                                </span>
                                <span class="radio-indicator"></span>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50 {{ $errors->has('privacy') ? 'border-red-300' : '' }}">
                                <input type="radio" name="privacy" value="public" class="sr-only" {{ old('privacy') === 'public' ? 'checked' : '' }}>
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="block text-sm font-medium text-gray-900">Public Team</span>
                                        </span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">
                                            Team is discoverable. Anyone can request to join.
                                        </span>
                                    </span>
                                </span>
                                <span class="radio-indicator"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="generate_code" name="generate_code" type="checkbox" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ old('generate_code', true) ? 'checked' : '' }}>
                                <label for="generate_code" class="ml-2 block text-sm text-gray-900">
                                    Generate joining code
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 ml-6">
                                Creates a unique code that others can use to join your team
                            </p>

                            <div class="flex items-center">
                                <input id="auto_activate" name="auto_activate" type="checkbox" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" {{ old('auto_activate', true) ? 'checked' : '' }}>
                                <label for="auto_activate" class="ml-2 block text-sm text-gray-900">
                                    Set as active team
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 ml-6">
                                Automatically switch to this team after creation
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters
            const nameInput = document.querySelector('input[name="name"]');
            const nameCounter = document.getElementById('name-counter');
            const descriptionInput = document.getElementById('description');
            const descriptionCounter = document.getElementById('description-counter');

            if (nameInput && nameCounter) {
                nameInput.addEventListener('input', function() {
                    nameCounter.textContent = this.value.length;
                });
                // Initialize counter
                nameCounter.textContent = nameInput.value.length;
            }

            if (descriptionInput && descriptionCounter) {
                descriptionInput.addEventListener('input', function() {
                    descriptionCounter.textContent = this.value.length;
                });
                // Initialize counter
                descriptionCounter.textContent = descriptionInput.value.length;
            }

            // Radio button styling
            const radioInputs = document.querySelectorAll('input[type="radio"]');
            radioInputs.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    // Remove selected state from all radio groups with the same name
                    const sameNameRadios = document.querySelectorAll(`input[name="${this.name}"]`);
                    sameNameRadios.forEach(function(r) {
                        r.closest('label').classList.remove('ring-2', 'ring-primary-500', 'border-primary-500');
                    });

                    // Add selected state to the checked radio
                    if (this.checked) {
                        this.closest('label').classList.add('ring-2', 'ring-primary-500', 'border-primary-500');
                    }
                });

                // Initialize selected state
                if (radio.checked) {
                    radio.closest('label').classList.add('ring-2', 'ring-primary-500', 'border-primary-500');
                }
            });
        });
    </script>
</x-layouts.app>
