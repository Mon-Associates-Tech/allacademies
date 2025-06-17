@props(['title' => null, 'action' => null, 'breadcrumb' => null])
<x-layouts.app>
    <div class="w-full min-h-screen flex flex-col h-full mx-auto max-w-7xl px-2 lg:px-0">
        <div class="print:hidden">
            <div class="">
                {{ $breadcrumb }}
            </div>
        </div>

        <x-alert.success/>
        <x-alert.danger/>
        <main class="flex-grow">
            <div class="flex mx-auto justify-between py-2 print:hidden max-w-[60rem]">
                <div class="font-bold pl-1">{{$title}}</div>
                <div class="">{{$action}}</div>
            </div>
            <div>
                {{ $slot }}
            </div>

            <div class="text-xs bg-gray-100 w-[95%] text-gray-600 mt-5 print:hidden">
                <div class="flex justify-between items-center py-3">
                <span>&copy;
                    <script>
                        document.write(new Date().getFullYear().toString())
                    </script> Mon and Associates
                </span>
                    <span>{{ App\Support\Version::full() }}</span>
                </div>
            </div>
        </main>



    </div>


</x-layouts.app>
