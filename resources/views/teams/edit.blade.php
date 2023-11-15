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
    @if (!$team->is_personal && Auth::user()->id === $team->owner_id)
        @if ($team->joining_code)
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2"> 
                    <div x-data="{ code: @js($team->joining_code)}" class="flex items-center justify-between py-4 pl-4 pr-5 mb-4 text-sm leading-6 rounded-md bg-gray-50">
                        <div class="flex w-0 flex-1 items-center">
                            <span class="truncate font-medium">Joining Code <br> 
                                <span class="rounded-sm mt-2 px-5 py-2.5 text-center inline-flex items-center border border-gray-200"> {{$team->joining_code}}</span>
                            </span>
                        </div>
                        <div class="ml-4 flex-shrink-0 space-x-6">
                            <div x-data="{ show: false }" class="relative inline-block">
                                <button x-on:click.away="show = false" x-on:click="navigator.clipboard && navigator.clipboard.writeText(code).then(() => show = true, setTimeout(() => show = false, 1000)).catch(() => {})" type="button" class="text-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 border border-gray-400 bg-white hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 me-2 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                    </svg>
                                    Copy
                                </button>
                                <span x-cloak x-show="show" class="absolute -bottom-6 left-1/2 -translate-x-1/2 inline-flex items-center rounded-md bg-green-50 px-1.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Copied!</span>
                            </div>
                            <form class="inline" method="POST" action="{{ route('teams.code', ['team' => $team]) }}">
                                @csrf
                                <button class="text-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center border border-gray-400 bg-gray-50 hover:bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                      </svg>
                                    Re-generate
                                </button>  
                            </form>
                            <button x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete joining code for {{ $team->name }}', '{{ route('teams.delete-code', ['team' => $team]) }}', 'Delete')" class="text-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center me-2 border border-gray-400 bg-white hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 me-2 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>

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
