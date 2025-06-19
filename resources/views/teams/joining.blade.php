<x-layouts.app title="Join Team" :has-action="false" title-align-center="true">


    <div class="max-w-xl mx-auto py-10 bg-white rounded-md sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('teams.add-member') }}">
            @csrf
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <x-form.input name="code" type="text" />
                </div>
                <div class=" text-end col-span-3">
                    <x-button.primary class="ml-2">Join Team</x-button.primary>
                </div>
            </div>

        </form>
    </div>

</x-layouts.app>
