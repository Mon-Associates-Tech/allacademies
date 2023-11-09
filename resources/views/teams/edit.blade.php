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
    @if (!$team->is_personal)
        @if ($team->joining_code)
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2"> 
                    <label class="block text-sm tracking-wide font-medium text-gray-700 mb-1">Members Joining Code</label>
                    <div x-data="{ code: @js($team->joining_code)}" class="flex items-center justify-between py-3 pl-4 pr-5 mb-4 text-sm leading-6 rounded-md border bg-blue-50">
                        <div class="flex w-0 flex-1 items-center">
                            <div x-data="{ show: false }" class="relative inline-block">
                                <button x-on:click.away="show = false" x-on:click="navigator.clipboard && navigator.clipboard.writeText(code).then(() => show = true, setTimeout(() => show = false, 1000)).catch(() => {})" type="button" class="flex items-center justify-center px-4 py-1 rounded-md space-x-2 border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400" type="button">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                    </svg>
                                    <span>copy</span>
                                </button>
                                <span x-cloak x-show="show" class="absolute -top-6 left-1/2 -translate-x-1/2 inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Copied!</span>
                              </div>
                            <span class="truncate font-medium ml-4">{{$team->joining_code}}</span>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <form class="inline" method="POST" action="{{ route('teams.code', ['team' => $team]) }}">
                                @csrf
                                <button class="text-blue-700 hover:text-blue-600 border border-gray-200 px-4 py-1 rounded-md space-x-2 hover:bg-blue-200">Re-Generate Code</button>
                            </form>
                            
                        </div>
                    </div> 
                </div>
            </div>
        @else
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <div class="rounded-md bg-blue-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                            <p class="text-sm text-blue-700">Generate a <strong>code</strong> that other users can use to join your team.</p>
                                <form class="inline" method="POST" action="{{ route('teams.code', ['team' => $team]) }}" class="mt-3 text-sm md:ml-6 md:mt-0">
                                    @csrf
                                    <button class="text-blue-700 hover:text-blue-600 text-sm px-4 py-2 rounded-md space-x-2 border border-blue-200 hover:bg-blue-200">Generate Code</button>
                                </form>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>  
        @endif
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
