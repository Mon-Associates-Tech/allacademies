@props(['title', 'action' => null, 'breadcrumb' => null])

@push('head')
    <script>
        document.addEventListener('alpine:initializing', () => {
            Alpine.store('deleteForm', {
                open: false,
                title: null,
                content: null,
                action: null,
                button: null,
                show(title, content, action, button = 'Delete') {
                    this.title = title
                    this.content = content
                    this.action = action
                    this.button = button
                    this.open = true
                },
                hide() {
                    this.open = false
                }
            })
        })
    </script>
@endpush

<x-app>
    <div class="w-full min-h-screen mx-auto max-w-7xl py-8 px-2 lg:px-0 space-y-5">
        <div class="flex justify-between items-center bg-white rounded-xl px-4 py-3 shadow print:hidden">
            <x-title :name="config('app.name')" :image="asset('img/logo.png')" />
            <div class='flex items-center justify-between w-1/2'>
                {{-- Notification and other links --}}

                <a href="{{ route('dashboard') }}" class='text-lg font-semibold hover:text-blue-400 '>Dashboard</a>
                <a href="{{ route('subscriptions.index') }}" class='text-lg font-semibold hover:text-blue-400 '>Subscriptions</a>
                <a href="{{ route('teams.index') }}" class='text-lg font-semibold hover:text-blue-400 '>Team</a>
                <a href="{{ route('profile.show') }}"  class='text-lg font-semibold hover:text-blue-400 '>Profile</a>
            </div>
            <div class="flex items-center">
                <div class="relative ml-5" x-data="{ open: false }">
                    <img x-on:click="open = !open" x-on:click.outside="open = false" src="{{ auth()->user()->avatar_url }}"
                        alt="{{ auth()->user()->name }} Logo" loading="lazy" class="w-8 h-8 rounded-lg cursor-pointer">
                    <div x-cloak x-show="open" x-transition
                        class="origin-top-right absolute z-10 right-0 bg-white rounded-lg w-56 mt-1 shadow-md border border-gray-200">
                        <div class="divide-y">
                            <div class="py-3 px-4 text-sm">
                                <p class="font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                <p class="text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('dashboard') }}"
                                    class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>

                                    <span class="ml-2">Dashboard</span>
                                </a>
                                <a href="{{ route('subscriptions.index') }}"
                                    class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>


                                    <span class="ml-2">Subscriptions</span>
                                </a>
                                @can('moderate')
                                    <a href="{{ route('academic-groups.index') }}"
                                        class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 011.872 1.002l.164.246a2.25 2.25 0 001.872 1.002h2.092a2.25 2.25 0 001.872-1.002l.164-.246A2.25 2.25 0 0116.954 9h4.636M2.41 9a2.25 2.25 0 00-.16.832V12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 01.382-.632l3.285-3.832a2.25 2.25 0 011.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0021.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 002.25 2.25z" />
                                        </svg>


                                        <span class="ml-2">Academic Groups</span>
                                    </a>
                                @endcan
                                @can('administrate')
                                    <a href="{{ route('users.index') }}"
                                        class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-5 h-5 text-gray-500 group-hover:text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>



                                        <span class="ml-2">Users</span>
                                    </a>
                                    <a href="{{ route('payments.index') }}"
                                        class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">

                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                        </svg>


                                        <span class="ml-2">Payments</span>
                                    </a>
                                @endcan
                                @can('own')
                                    <a href="{{ route('settings.index') }}"
                                        class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                                        </svg>

                                        <span class="ml-2">Settings</span>
                                    </a>
                                @endcan
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.show') }}"
                                    class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>

                                    <span class="ml-2">Profile</span>
                                </a>
                                <a href="{{ route('teams.index') }}"
                                    class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>

                                    <span class="ml-2">Team</span>
                                </a>
                                @can('administrate')
                                    <a href="{{ route('audit-teams.index') }}"
                                        class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">

                                        <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                        </svg>



                                        <span class="ml-2">Audit Teams</span>
                                    </a>
                                @endcan
                                <a href="{{ route('security') }}"
                                    class="flex items-center py-2 px-4 group cursor-pointer text-sm tracking-wide text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>

                                    <span class="ml-2">Security</span>
                                </a>
                            </div>
                            <form method="POST" action="{{ route('sign-out') }}" class="py-1">
                                @csrf
                                <button
                                    class="w-full flex items-center py-2 px-4 text-sm group text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                                    <svg class="w-5 h-5 text-red-500 group-hover:text-red-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                    </svg>

                                    <span class="ml-2">Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="print:hidden">
            {{ $breadcrumb }}
            <div class="mt-2 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $title }}
                    </h2>
                </div>
                <div class="mt-4 flex-shrink-0 flex md:mt-0 md:ml-4">
                    {{ $action }}
                </div>
            </div>
        </div>

        <x-alert.success />
        <x-alert.danger />

        <div>
            {{ $slot }}
        </div>

        <div class="text-xs bg-gray-100 w-[95%] text-gray-600 mt-5 print:hidden">
            <div class="flex justify-between items-center">
                <span>&copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Mon and Associates
                </span>
                <span>{{ App\Support\Version::full() }}</span>
            </div>
        </div>
    </div>


</x-app>
