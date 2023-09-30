<x-auth title="Edit Academic Subject">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Pending Teams' => route('manage-teams.index'),
        ]" />
    </x-slot>

    <form method="POST" action="">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$team->name" />
            </div>
            <div>
                <x-form.input name="code" type="text" :value="$team->name" />
            </div>
        </div>

        <div class="relative mt-5">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="isolate inline-flex -space-x-px">
                    <button wire:click="minus()" type="button"
                        class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-l-lg">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-minus">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                    <button wire:click="plus()" type="button"
                        class="relative inline-flex items-center border border-gray-300 px-3 py-2 bg-white hover:bg-gray-50 rounded-r-lg">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-plus">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                </span>
            </div>
        </div>

        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Academic Subject</x-button.primary>
        </div>
    </form>
</x-auth>
