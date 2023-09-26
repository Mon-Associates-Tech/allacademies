<x-auth title="Edit Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>
    <div class="grid grid-cols-3 gap-4">
    @if(!$team->is_personal)
        @if($team->metaData)
            @if($team->status->value == 'approved')
                <div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-2 mb-3 mt-2 col-span-2">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary-600 hover:text-primary-900 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span class="font-bold mr-2">Approved</span> You are all set to create examinations. Institution details will be used for examination heading.
                    </div>
                </div> 
            @elseif($team->status->value == 'pending')
            <div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-2 mb-3 mt-2 col-span-2">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary-600 hover:text-primary-900 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                      </svg>
                    <span class="font-bold mr-2">Pending</span> Institution details pending approval. Details must be approved before you can create examinations because they will be used for examination heading.
                </div>
            </div>
            @elseif($team->status->value == 'declined')
            <div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-2 mb-3 mt-2 col-span-2">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-primary-600 hover:text-primary-900 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    <span class="font-bold mr-2">Declined</span> Institution details have been declined. Make the necessary changes and update team for review.
                </div>
            </div> 
            @endif
        @else
            <div class="bg-primary-50 border border-primary-300 text-sm text-gray-600 rounded-md p-4 mb-2 mt-2 col-span-2" role="alert">
                <span class="font-bold">Note!</span> 
                <span>Institution details must be provided. These details will be used for examination heading and they must be approved before you can create examinations.</span>
            </div>      
        @endif 
    @endif
    </div>
    <form method="POST" action="{{ route('teams.update', ['team' => $team]) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$team->name" />
                {{-- <input name="status" type="text" value="pending" hidden /> --}}
            </div>
            @if(!$team->is_personal)
            <div class="col-span-2 space-y-2">
                @livewire('institution-type', ['team' => $team])
            </div>
            @endif
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Team</x-button.primary>
        </div>
    </form>
</x-auth>