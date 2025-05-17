<x-auth title="New Team">


    <form method="POST" action="{{ route('teams.store') }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" />
            </div>
        </div>
        <div class=" mt-3">
            <x-button.primary class="ml-2">Create Team</x-button.primary>
        </div>
    </form>
</x-auth>
