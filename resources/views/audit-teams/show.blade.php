<x-layouts.app title="Team Audit: {{ $auditTeam->name }}">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams (Auditing)' => route('audit-teams.index'),
            $auditTeam->name => null,
        ]" />
    </x-slot>

    <!-- Team Information Header -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $auditTeam->name }}</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Owned by <strong>{{ $auditTeam->owner->name }}</strong>
                        <span class="text-gray-400">•</span>
                        Submitted {{ $auditTeam->updated_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Pending Review
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Changes Overview -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        @foreach ($audits as [$heading, $current, $incoming, $changes])
            @if ($incoming)
                <x-audit.change-card
                    :heading="$heading"
                    :current="$current"
                    :incoming="$incoming"
                    :changes="$changes"
                />
            @endif
        @endforeach
    </div>

    <!-- Decision Actions -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Review Decision</h3>
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <p>Review these institutional changes and decide whether to approve or decline them.</p>
                    <p class="mt-1">Once approved, the changes will be applied to the team's institutional information.</p>
                </div>
                <div class="flex space-x-3">
                    <x-link.secondary
                        :to="route('audit-teams.reason', ['audit_team' => $auditTeam])"
                        class="inline-flex items-center"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Decline
                    </x-link.secondary>

                    <form class="inline" method="POST" action="{{ route('audit-teams.approve', ['audit_team' => $auditTeam]) }}">
                        @csrf
                        <x-button.primary class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Approve Changes
                        </x-button.primary>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
