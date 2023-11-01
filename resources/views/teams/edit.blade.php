<x-auth title="Edit Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>

    @if (!$team->is_personal && $team->status == \App\Enums\TeamStatus::PENDING)
    <div class="rounded-md bg-yellow-50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
            </div>
            <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Changes Pending</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>Changes made to your institutional information are currently pending approval. The status should be updated soon.</p>
            </div>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('teams.update', ['team' => $team]) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$team->name" />
                @if (!$team->is_personal)
                    <div class="pt-4">
                        @livewire('institutional-information', ['team' => $team])
                    </div>
                @endif
            </div>
            @isset ($team->meta['logo'])
            <div class="col-span-1">
                <div class="relative">
                    <img class="inline-block h-auto w-1/2 rounded-md border border-gray-300 shadow-sm" src="{{ Storage::disk('s3')->url($team->meta['logo']) }}" alt="">
                    <span class="absolute left-0 bottom-0 p-1 text-sm font-medium bg-white rounded-bl-md rounded-tr-md">Current Logo</span>
                </div>
            </div>
            @endif
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Team</x-button.primary>
        </div>
    </form>
</x-auth>
