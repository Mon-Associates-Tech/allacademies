<x-layouts.app title="Edit Member">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
            'Members' => route('teams.members.index', ['team' => $team]),
            'Edit Member' => null
        ]" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Team Member</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Update member role and permissions for <span class="font-medium">{{ $team->name }}</span>
                    </p>
                </div>

                <!-- Member Avatar & Quick Info -->
                <div class="flex items-center space-x-3">
                    @if($member->avatar)
                        <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
                             src="{{ $member->avatar }}"
                             alt="{{ $member->name }}">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center border-2 border-gray-200">
                            <span class="text-white font-semibold text-sm">
                                {{ strtoupper(substr($member->name ?? $member->email, 0, 2)) }}
                            </span>
                        </div>
                    @endif
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $member->name ?? 'User' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Current: {{ ucfirst($member->pivot->role) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('members.update', ['team' => $team, 'member' => $member]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Member Information Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/10 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Member Information
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Basic member details and contact information</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Email (Read-only) -->
                        <div class="sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="email"
                                       name="email"
                                       value="{{ $member->email }}"
                                       readonly
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm focus:outline-none cursor-not-allowed">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email address cannot be changed</p>
                        </div>

                        <!-- Member Since -->
                        <div class="sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Member Since
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       value="{{ $member->pivot->created_at->format('M j, Y \a\t g:i A') }}"
                                       readonly
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm focus:outline-none cursor-not-allowed">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">When this member joined the team</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions Card -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-gray-700/10 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Role & Permissions
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select the appropriate role for this team member</p>
                </div>

                <div class="p-6">
                    <!-- Role Selection -->
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Team Role
                        </label>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Member Role -->
                            <div class="relative">
                                <input type="radio"
                                       id="role_member"
                                       name="role"
                                       value="member"
                                       {{ $member->pivot->role === 'member' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <label for="role_member"
                                       class="flex p-4 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                    <div class="flex items-start w-full">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Member</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Standard team member with basic access
                                            </div>
                                            <ul class="mt-2 text-xs text-gray-400 dark:text-gray-500 space-y-1">
                                                <li>• View team content</li>
                                                <li>• Participate in activities</li>
                                                <li>• Access shared resources</li>
                                            </ul>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Admin Role -->
                            <div class="relative">
                                <input type="radio"
                                       id="role_admin"
                                       name="role"
                                       value="admin"
                                       {{ $member->pivot->role === 'admin' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <label for="role_admin"
                                       class="flex p-4 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer peer-checked:border-red-600 peer-checked:ring-2 peer-checked:ring-red-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                                    <div class="flex items-start w-full">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">Administrator</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Full administrative access to the team
                                            </div>
                                            <ul class="mt-2 text-xs text-gray-400 dark:text-gray-500 space-y-1">
                                                <li>• All member permissions</li>
                                                <li>• Manage team settings</li>
                                                <li>• Add/remove members</li>
                                                <li>• Assign roles</li>
                                            </ul>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('role')
                        <div class="mt-2 flex items-center text-sm text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Current Status Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border border-blue-200 dark:border-gray-600">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200">Current Member Status</h4>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p><strong>{{ $member->name ?? $member->email }}</strong> is currently a <strong>{{ ucfirst($member->pivot->role) }}</strong> of this team.</p>
                            @if($member->pivot->role === 'admin')
                                <p class="mt-1 text-xs">⚠️ Changing from Admin to Member will remove all administrative privileges.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-link.secondary :to="route('teams.members.index', ['team' => $team])">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Members
                </x-link.secondary>

                <div class="flex space-x-3">
                    <!-- Reset Button -->
                    <button type="button"
                            onclick="resetForm()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>

                    <!-- Update Button -->
                    <x-button.primary type="submit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Member Role
                    </x-button.primary>
                </div>
            </div>
        </form>
    </div>

    <!-- JavaScript for enhanced interactivity -->
    <script>
        function resetForm() {
            // Reset radio buttons to original values
            const originalRole = '{{ $member->pivot->role }}';
            document.querySelector(`input[name="role"][value="${originalRole}"]`).checked = true;
        }

        // Add confirmation for role changes
        document.querySelector('form').addEventListener('submit', function(e) {
            const currentRole = '{{ $member->pivot->role }}';
            const selectedRole = document.querySelector('input[name="role"]:checked').value;

            if (currentRole !== selectedRole) {
                const memberName = '{{ $member->name ?? $member->email }}';
                const confirmMessage = `Are you sure you want to change ${memberName}'s role from ${currentRole} to ${selectedRole}?`;

                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                }
            }
        });
    </script>
</x-layouts.app>
