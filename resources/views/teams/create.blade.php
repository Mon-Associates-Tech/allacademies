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
                            class="team-name-input"
                            maxlength="100"
                        />
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
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                            placeholder="Describe your team's purpose, goals, or focus areas..."
                            maxlength="500"
                        >{{ old('description') }}</textarea>
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
                            Team Type
                        </label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50">
                                <input type="radio" name="type" value="academic" class="sr-only" checked>
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

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50">
                                <input type="radio" name="type" value="professional" class="sr-only">
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

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50">
                                <input type="radio" name="type" value="personal" class="sr-only">
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
                            Privacy Settings
                        </label>
                        <div class="space-y-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50">
                                <input type="radio" name="privacy" value="private" class="sr-only" checked>
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

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-gray-50">
                                <input type="radio" name="privacy" value="public" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="block text-sm font-medium text-gray-900">Public Team</span>
                                        </span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">
                                            Team is discoverable and anyone can request to join.
                                        </span>
                                    </span>
                                </span>
                                <span class="radio-indicator"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Initial Settings -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Initial Settings
                        </label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input
                                    id="generate_code"
                                    name="generate_code"
                                    type="checkbox"
                                    checked
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                >
                                <label for="generate_code" class="ml-3 block text-sm text-gray-700">
                                    Generate joining code immediately
                                    <span class="block text-xs text-gray-500">Members can join using an 8-character code</span>
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input
                                    id="auto_activate"
                                    name="auto_activate"
                                    type="checkbox"
                                    checked
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                >
                                <label for="auto_activate" class="ml-3 block text-sm text-gray-700">
                                    Activate team immediately
                                    <span class="block text-xs text-gray-500">Team becomes active and ready for collaboration</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6">
                        <x-button.primary type="submit" class="flex-1 justify-center">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Team
                        </x-button.primary>

                        <a href="{{ route('teams.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Cancel
                        </a>
                    </div>
                </form>

                <!-- Help Section -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="text-center">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Need Help?</h3>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 text-xs">
                            <a href="#" class="text-primary-600 hover:text-primary-500">
                                Team Management Guide
                            </a>
                            <a href="#" class="text-primary-600 hover:text-primary-500">
                                Collaboration Best Practices
                            </a>
                            <a href="{{ route('teams.joining') }}" class="text-primary-600 hover:text-primary-500">
                                Join Existing Team
                            </a>
                            <a href="#" class="text-primary-600 hover:text-primary-500">
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters
            const nameInput = document.querySelector('input[name="name"]');
            const descriptionInput = document.querySelector('textarea[name="description"]');
            const nameCounter = document.getElementById('name-counter');
            const descriptionCounter = document.getElementById('description-counter');

            function updateCounter(input, counter) {
                const length = input.value.length;
                counter.textContent = length;

                // Update color based on length
                if (length > input.maxLength * 0.9) {
                    counter.classList.add('text-red-500');
                    counter.classList.remove('text-gray-400', 'text-yellow-500');
                } else if (length > input.maxLength * 0.7) {
                    counter.classList.add('text-yellow-500');
                    counter.classList.remove('text-gray-400', 'text-red-500');
                } else {
                    counter.classList.add('text-gray-400');
                    counter.classList.remove('text-red-500', 'text-yellow-500');
                }
            }

            if (nameInput && nameCounter) {
                nameInput.addEventListener('input', () => updateCounter(nameInput, nameCounter));
                updateCounter(nameInput, nameCounter); // Initialize
            }

            if (descriptionInput && descriptionCounter) {
                descriptionInput.addEventListener('input', () => updateCounter(descriptionInput, descriptionCounter));
                updateCounter(descriptionInput, descriptionCounter); // Initialize
            }

            // Radio button styling
            const radioInputs = document.querySelectorAll('input[type="radio"]');
            radioInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Remove selected state from all radio groups with same name
                    const groupInputs = document.querySelectorAll(`input[name="${this.name}"]`);
                    groupInputs.forEach(groupInput => {
                        const label = groupInput.closest('label');
                        if (groupInput.checked) {
                            label.classList.add('border-primary-500', 'ring-2', 'ring-primary-500');
                            label.classList.remove('border-gray-300');
                        } else {
                            label.classList.remove('border-primary-500', 'ring-2', 'ring-primary-500');
                            label.classList.add('border-gray-300');
                        }
                    });
                });
            });

            // Initialize radio button styling
            radioInputs.forEach(input => {
                if (input.checked) {
                    input.dispatchEvent(new Event('change'));
                }
            });

            // Form validation
            const form = document.getElementById('team-creation-form');
            form.addEventListener('submit', function(e) {
                const teamName = nameInput.value.trim();

                if (teamName.length < 2) {
                    e.preventDefault();
                    alert('Team name must be at least 2 characters long.');
                    nameInput.focus();
                    return false;
                }

                if (teamName.length > 100) {
                    e.preventDefault();
                    alert('Team name cannot exceed 100 characters.');
                    nameInput.focus();
                    return false;
                }
            });

            // Auto-focus on name input
            if (nameInput) {
                nameInput.focus();
            }
        });
    </script>

    <!-- Custom CSS for radio indicators -->
    <style>
        .radio-indicator {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            background: white;
        }

        input[type="radio"]:checked + span + .radio-indicator {
            border-color: #3b82f6;
            background-color: #3b82f6;
        }

        input[type="radio"]:checked + span + .radio-indicator::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }
    </style>
</x-layouts.app>
