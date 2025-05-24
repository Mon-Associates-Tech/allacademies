<x-layouts.app title="Security">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    <div>
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Manage password
                        </h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            <p>
                                Is your password old and outdated? Is your account compromised by another. Simple change
                                your password to be in charge again.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-0 sm:ml-6 sm:flex-shrink-0 sm:flex sm:items-center">
                        <x-link.primary :to="route('password.change')">
                            Change Password
                        </x-link.primary>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
