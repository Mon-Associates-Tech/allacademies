<x-auth title="Edit Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>

    @if (!$team->is_personal && $team->status == \App\Enums\TeamStatus::PENDING)
    <div class="rounded-md bg-yellow-50 p-4 mb-8">
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
    @if (!$team->is_personal && Auth::user()->id === $team->owner_id)
        @if ($team->joining_code)
            <div class="grid grid-cols-3">
                <div class="col-span-2"> 
                    <label class="block text-sm tracking-wide font-medium text-gray-700 mb-1">Joining Code</label>
                    <div x-data="{ code: @js($team->joining_code)}" class="flex items-center justify-between py-4 bg-white border border-gray-300 rounded-lg shadow-sm w-full leading-tight mb-4 px-4">
                        <div class="flex w-0 flex-1 items-center text-center">
                            <input name="code" type="text" class="border-gray-300 rounded-l-lg shadow-sm w-full leading-tight" value="{{$team->joining_code}}" disabled/>
                        </div>
                        <div class="inline-flex shadow-sm rounded-md" role="group">
                            <div x-data="{ show: false }" class="relative inline-block">
                                <button x-on:click.away="show = false" x-on:click="navigator.clipboard && navigator.clipboard.writeText(code).then(() => show = true, setTimeout(() => show = false, 1000)).catch(() => {})" type="button" class="border border-gray-200 bg-white text-sm font-medium px-6 py-2 text-gray-900 hover:bg-gray-100 hover:text-blue-700">
                                Copy
                                </button>
                                <span x-cloak x-show="show" class="absolute -top-6 left-1/2 -translate-x-1/2 inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Copied!</span>
                            </div>
                            <form class="inline" method="POST" action="{{ route('teams.code', ['team' => $team]) }}">
                                @csrf
                                <button type="submit" class="border-t border-b border-gray-200 bg-white text-sm font-medium px-6 py-2 text-gray-900 hover:bg-gray-100 hover:text-blue-700">
                                Re-generate
                                </button>
                            </form>
                            <button x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete joining code for {{ $team->name }}', '{{ route('teams.delete-code', ['team' => $team]) }}', 'Delete')" class="rounded-r-md border border-gray-200 bg-white text-sm font-medium px-6 py-2 text-gray-900 hover:bg-gray-100 hover:text-blue-700">
                              Delete
                            </button>
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
                                <form class="inline" method="POST" action="{{  route('teams.code', ['team' => $team]) }}">
                                    @csrf
                                    <button class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600 flex flex-inline">
                                        Generate Code
                                        <span aria-hidden="true"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 ml-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </span>
                                    </button>
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
