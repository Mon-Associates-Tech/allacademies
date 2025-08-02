<x-layouts.app title="Edit Team: {{ $team->name }}" :has-action="false" title-align-center="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
            $team->name => '#'
        ]" />
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-semibold text-gray-900">Edit Team</h1>
                                <p class="text-sm text-gray-500">Manage your team settings and configuration</p>
                            </div>
                        </div>

                        <!-- Team Status Badge -->
                        @if($team->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Alert -->
            @if (!$team->is_personal && $team->status == \App\Enums\TeamStatus::PENDING)
                <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Changes Pending Approval</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Your institutional information changes are currently under review. The approval status will be updated soon.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                            <p class="mt-1 text-sm text-gray-500">Update your team's basic details and settings</p>
                        </div>

                        <form method="POST" action="{{ route('teams.update', ['team' => $team]) }}" enctype="multipart/form-data" id="team-edit-form">
                            @csrf
                            @method('PATCH')

                            <div class="px-6 py-4 space-y-6">
                                <!-- Team Name -->
                                <div>
                                    <x-form.input
                                        name="name"
                                        type="text"
                                        :value="$team->name"
                                        placeholder="Enter team name"
                                        required
                                        maxlength="100"
                                        class="team-name-input"
                                    />
                                    <div class="mt-1 flex justify-between text-xs">
                                        <span class="text-gray-500">Choose a unique and descriptive name for your team</span>
                                        <span class="text-gray-400">
                                            <span id="name-counter">{{ strlen($team->name) }}</span>/100
                                        </span>
                                    </div>
                                </div>

                                <!-- Team Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        rows="4"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                        placeholder="Describe your team's purpose and goals..."
                                        maxlength="500"
                                    >{{ old('description', $team->description) }}</textarea>
                                    <div class="mt-1 flex justify-between text-xs">
                                        <span class="text-gray-500">Help team members understand what this team is about</span>
                                        <span class="text-gray-400">
                                            <span id="description-counter">{{ strlen($team->description ?? '') }}</span>/500
                                        </span>
                                    </div>
                                </div>

                                <!-- Team Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Team Type</label>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        @foreach(['academic' => ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'label' => 'Academic', 'description' => 'Study groups and educational projects'], 'professional' => ['icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6z', 'label' => 'Professional', 'description' => 'Work projects and collaboration'], 'personal' => ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'label' => 'Personal', 'description' => 'Personal projects and hobbies']] as $type => $config)
                                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50">
                                                <input type="radio" name="type" value="{{ $type }}" class="sr-only" {{ old('type', $team->type) === $type ? 'checked' : '' }}>
                                                <span class="flex flex-1">
                                                    <span class="flex flex-col">
                                                        <span class="flex items-center">
                                                            <svg class="h-4 w-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}" />
                                                            </svg>
                                                            <span class="block text-sm font-medium text-gray-900">{{ $config['label'] }}</span>
                                                        </span>
                                                        <span class="mt-1 text-xs text-gray-500">{{ $config['description'] }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Privacy Settings -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Privacy Settings</label>
                                    <div class="space-y-3">
                                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50">
                                            <input type="radio" name="privacy" value="private" class="sr-only" {{ old('privacy', $team->privacy) === 'private' ? 'checked' : '' }}>
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="flex items-center">
                                                        <svg class="h-4 w-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                        </svg>
                                                        <span class="block text-sm font-medium text-gray-900">Private Team</span>
                                                    </span>
                                                    <span class="mt-1 text-xs text-gray-500">Only members can see team content. Members join by invitation or code.</span>
                                                </span>
                                            </span>
                                        </label>

                                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none hover:bg-gray-50">
                                            <input type="radio" name="privacy" value="public" class="sr-only" {{ old('privacy', $team->privacy) === 'public' ? 'checked' : '' }}>
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="flex items-center">
                                                        <svg class="h-4 w-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="block text-sm font-medium text-gray-900">Public Team</span>
                                                    </span>
                                                    <span class="mt-1 text-xs text-gray-500">Team is discoverable and anyone can request to join.</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Logo Upload -->
                                <div>
                                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Team Logo</label>

                                    @isset($team->meta['logo'])
                                        <div class="mb-4">
                                            <div class="relative inline-block">
                                                <img class="h-20 w-20 rounded-lg border border-gray-300 shadow-sm object-cover" src="{{ Storage::disk('s3')->url($team->meta['logo']) }}" alt="Current Logo">
                                                <span class="absolute -bottom-2 -right-2 bg-white text-xs text-gray-600 px-2 py-1 rounded-full border border-gray-200">Current</span>
                                            </div>
                                        </div>
                                    @endisset

                                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                    <span>Upload a logo</span>
                                                    <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Institutional Information (if not personal team) -->
                                @if (!$team->is_personal)
                                    <div class="pt-6 border-t border-gray-200">
                                        <h3 class="text-sm font-medium text-gray-900 mb-4">Institutional Information</h3>
                                        @livewire('institutional-information', ['team' => $team])
                                    </div>
                                @endif
                            </div>

                            <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between gap-3">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Last updated: {{ $team->updated_at->format('M j, Y g:i A') }}
                                </div>

                                <div class="flex gap-3">
                                    <a href="{{ route('teams.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Cancel
                                    </a>
                                    <x-button.primary type="submit" class="inline-flex items-center">
                                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Update Team
                                    </x-button.primary>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">

                    <!-- Joining Code Management -->
                    @if (!$team->is_personal && Auth::user()->id === $team->owner_id)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Joining Code</h3>
                                <p class="mt-1 text-sm text-gray-500">Manage how people join your team</p>
                            </div>

                            <div class="px-6 py-4">
                                @if ($team->joining_code)
                                    <div class="text-center">
                                        <div class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                            <span class="font-mono text-lg font-bold text-gray-900">{{ $team->joining_code }}</span>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600">Share this code with others to let them join your team</p>

                                        <div class="mt-4 flex flex-col gap-2">
                                            <div x-data="{ show: false }" class="relative">
                                                <button
                                                    x-on:click.away="show = false"
                                                    x-on:click="navigator.clipboard && navigator.clipboard.writeText('{{ $team->joining_code }}').then(() => { show = true; setTimeout(() => show = false, 1000) }).catch(() => {})"
                                                    type="button"
                                                    class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                                >
                                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    Copy Code
                                                </button>
                                                <span x-cloak x-show="show" class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Copied!
                                                </span>
                                            </div>

                                            <form method="POST" action="{{ route('teams.code', ['team' => $team]) }}">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    Regenerate
                                                </button>
                                            </form>

                                            <button
                                                x-data="{}"
                                                x-on:click="$store.deleteForm.show('Remove Joining Code', 'Are you sure you want to remove the joining code for {{ $team->name }}? New members won\\'t be able to join using a code.', '{{ route('teams.remove-code', ['team' => $team]) }}', 'Remove Code')"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            >
                                                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Remove Code
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Joining Code</h3>
                                        <p class="mt-1 text-sm text-gray-500">Generate a code to let others join your team easily</p>
                                        <div class="mt-4">
                                            <form method="POST" action="{{ route('teams.code', ['team' => $team]) }}">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Generate Code
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Team Statistics -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Team Overview</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Members</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $team->members->count() ?? 0 }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Created</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $team->created_at->format('M j, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Type</dt>
                                    <dd class="text-sm font-medium text-gray-900 capitalize">{{ $team->type ?? 'Academic' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Privacy</dt>
                                    <dd class="text-sm font-medium text-gray-900 capitalize">{{ $team->privacy ?? 'Private' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="px-6 py-3 border-t border-gray-200">
                            <a href="{{ route('teams.members.index', $team) }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                                Manage members →
                            </a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            @if(!$team->is_active)
                                <form method="POST" action="{{ route('teams.activate', $team) }}">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 border border-green-300 text-sm font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Activate Team
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('teams.members.index', $team) }}" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                View Members
                            </a>

                            <a href="{{ route('teams.index') }}" class="w-full inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Teams
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
            }

            if (descriptionInput && descriptionCounter) {
                descriptionInput.addEventListener('input', () => updateCounter(descriptionInput, descriptionCounter));
            }

            // Radio button styling
            const radioInputs = document.querySelectorAll('input[type="radio"]');
            radioInputs.forEach(input => {
                input.addEventListener('change', function() {
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

                // Initialize styling for checked inputs
                if (input.checked) {
                    input.dispatchEvent(new Event('change'));
                }
            });

            // File upload preview
            const logoInput = document.getElementById('logo');
            if (logoInput) {
                logoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // You can add image preview functionality here
                            console.log('Image selected:', file.name);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Form validation
            const form = document.getElementById('team-edit-form');
            form.addEventListener('submit', function(e) {
                const teamName = nameInput.value.trim();

                if (teamName.length < 2) {
                    e.preventDefault();
                    alert('Team name must be at least 2 characters long.');
                    nameInput.focus();
                    return false;
                }
            });
        });
    </script>
</x-layouts.app>
