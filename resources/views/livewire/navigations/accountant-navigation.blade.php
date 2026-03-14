<div>
    <h3 class="text-xs hidden uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">Financial Management</span>
    </h3>
    <ul class="mt-3" x-data="{ sidebarExpanded: $store.sidebar.expanded }">
        <!-- Dashboard -->
        <li class="mb-0.5 last:mb-0" title="Dashboard">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('accountant.dashboard') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('accountant.dashboard')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('accountant.dashboard') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Dashboard</span>
                </div>
            </a>
        </li>

        <!-- Transactions -->
        <li class="mb-0.5 last:mb-0" title="Payment Transactions">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('accountant.transactions*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('accountant.transactions.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('accountant.transactions*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Transactions</span>
                </div>
            </a>
        </li>

        <!-- Financial Reports -->
        <li class="mb-0.5 last:mb-0" title="Financial Reports & Analytics">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('accountant.reports*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('accountant.reports.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('accountant.reports*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Financial Reports</span>
                </div>
            </a>
        </li>

        <!-- Students & Payments -->
        <li class="mb-0.5 last:mb-0" title="Student Payment Tracking">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('accountant.students*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('accountant.students.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('accountant.students*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Students</span>
                </div>
            </a>
        </li>

        <!-- Financial Aid -->
        <li class="mb-0.5 last:mb-0" title="Financial Aid Programs">
            <a :class="sidebarExpanded ? 'py-2' : ''"
               class="block pl-3 rounded-lg transition {{ Route::is('accountant.financial-aid*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
               href="{{route('accountant.financial-aid.index')}}">
                <div class="flex items-center">
                    <svg class="shrink-0 fill-current {{ Route::is('accountant.financial-aid*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                         xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                    </svg>
                    <span class="text-sm ml-2 sidebar-text duration-200">Financial Aid</span>
                </div>
            </a>
        </li>

        <!-- Receipts & Documents -->
        <li class="mb-0.5 last:mb-0" title="Receipt Management" x-data="{ open: false }">
            <button @click="open = !open" :class="sidebarExpanded ? 'py-2' : ''"
                    class="w-full text-left pl-3 rounded-lg transition {{ Route::is('accountant.receipts*') ? 'bg-violet-500 text-white font-bold' : 'text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="shrink-0 fill-current {{ Route::is('accountant.receipts*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}"
                             xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Receipts</span>
                    </div>
                    <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200 {{ Route::is('accountant.receipts*') ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>
            <ul x-show="open" x-transition class="ml-6 mt-1 space-y-1">
                <li>
                    <a href="{{ route('accountant.transactions.index') }}?status=succeeded" 
                       class="block py-1 px-3 text-sm rounded transition {{ request()->get('status') === 'succeeded' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200' }}">
                        Generate Receipts
                    </a>
                </li>
                <li>
                    <a href="{{ route('accountant.reports.index') }}" 
                       class="block py-1 px-3 text-sm rounded transition text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                        Bulk Receipts
                    </a>
                </li>
            </ul>
        </li>

        <!-- Export & Analytics -->
        <li class="mb-0.5 last:mb-0" title="Data Export & Analytics" x-data="{ open: false }">
            <button @click="open = !open" :class="sidebarExpanded ? 'py-2' : ''"
                    class="w-full text-left pl-3 rounded-lg transition text-gray-800 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500"
                             xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M12,12L16,16H13.5V19H10.5V16H8L12,12Z"/>
                        </svg>
                        <span class="text-sm ml-2 sidebar-text duration-200">Export & Analytics</span>
                    </div>
                    <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform duration-200 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>
            <ul x-show="open" x-transition class="ml-6 mt-1 space-y-1">
                <li>
                    <a href="{{ route('accountant.transactions.export') }}" 
                       class="block py-1 px-3 text-sm rounded transition text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                        Export Transactions
                    </a>
                </li>
                <li>
                    <a href="{{ route('accountant.reports.export') }}" 
                       class="block py-1 px-3 text-sm rounded transition text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                        Export Reports
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
