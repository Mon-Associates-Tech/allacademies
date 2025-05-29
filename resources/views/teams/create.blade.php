<x-layouts.app title="New Team" :has-action="false" :title-align-center="true">


<div class="max-w-xl mx-auto py-10 bg-white rounded-md sm:px-6 lg:px-8">
    <form method="POST" action="{{ route('teams.store') }}">
        @csrf
        <div class="grid">
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" />
            </div>
        </div>
        <div class="mt-5 text-right">
            <x-button.primary class="ml-2">Create Team</x-button.primary>
        </div>
    </form>
</div>

</x-layouts.app>
