<div class="p-6 rounded-lg w-full min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 dark:text-white">Dashboard Overview</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $cards = [
                ['label' => 'Total Users', 'value' => $statistics['totalUsers'], 'icon' => '👥', 'bgLight' => 'bg-blue-50', 'bgDark' => 'dark:bg-blue-900/20', 'textColor' => 'text-blue-700', 'darkTextColor' => 'dark:text-blue-400'],
                ['label' => 'Total Students', 'value' => $statistics['totalStudents'], 'icon' => '🎓', 'bgLight' => 'bg-green-50', 'bgDark' => 'dark:bg-green-900/20', 'textColor' => 'text-green-700', 'darkTextColor' => 'dark:text-green-400'],
                ['label' => 'Total Books', 'value' => $statistics['totalBooks'], 'icon' => '📚', 'bgLight' => 'bg-yellow-50', 'bgDark' => 'dark:bg-yellow-900/20', 'textColor' => 'text-yellow-700', 'darkTextColor' => 'dark:text-yellow-400'],
                ['label' => 'Student Groups', 'value' => $statistics['totalGroups'], 'icon' => '👥', 'bgLight' => 'bg-red-50', 'bgDark' => 'dark:bg-red-900/20', 'textColor' => 'text-red-700', 'darkTextColor' => 'dark:text-red-400'],
                ['label' => 'Pending Approvals', 'value' => $statistics['pendingApprovals'], 'icon' => '⏳', 'bgLight' => 'bg-orange-50', 'bgDark' => 'dark:bg-orange-900/20', 'textColor' => 'text-orange-700', 'darkTextColor' => 'dark:text-orange-400', 'bgLight' => 'bg-orange-50', 'bgDark' => 'dark:bg-orange-900/20', 'textColor' => 'text-orange-700', 'darkTextColor' => 'dark:text-orange-400'],
                ['label' => 'Active Borrowings', 'value' => $statistics['activeBorrowings'], 'icon' => '📖', 'bgLight' => 'bg-teal-50', 'bgDark' => 'dark:bg-teal-900/20', 'textColor' => 'text-teal-700', 'darkTextColor' => 'dark:text-teal-400'],
                ['label' => 'Active Subscriptions', 'value' => $statistics['activeSubscriptions'], 'icon' => '✅', 'bgLight' => 'bg-emerald-50', 'bgDark' => 'dark:bg-emerald-900/20', 'textColor' => 'text-emerald-700', 'darkTextColor' => 'dark:text-emerald-400'],
                // Additional statistics for Teachers, Librarians, and Authors
                ['label' => 'Total Teachers', 'value' => $statistics['totalTeachers'], 'icon' => '🏫', 'bgLight' => 'bg-purple-50', 'bgDark' => 'dark:bg-purple-900/20', 'textColor' => 'text-purple-700', 'darkTextColor' => 'dark:text-purple-400'],
                ['label' => 'Total Librarians', 'value' => $statistics['totalLibrarians'], 'icon' => '📚', 'bgLight' => 'bg-indigo-50', 'bgDark' => 'dark:bg-indigo-900/20', 'textColor' => 'text-indigo-700', 'darkTextColor' => 'dark:text-indigo-400'],
                ['label' => 'Total Authors', 'value' => $statistics['totalAuthors'], 'icon' => '✍️', 'bgLight' => 'bg-pink-50', 'bgDark' => 'dark:bg-pink-900/20', 'textColor' => 'text-pink-700', 'darkTextColor' => 'dark:text-pink-400'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="{{ $card['bgLight'] }} {{ $card['bgDark'] }} p-6 rounded-xl shadow-sm hover:shadow transition-shadow duration-300 dark:shadow-gray-800">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-opacity-50 mr-4 {{ $card['textColor'] }} {{ $card['darkTextColor'] }}">
                        {{ $card['icon'] }}
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $card['label'] }}</h3>
                        <p class="text-2xl font-bold text-gray-800 mt-1 dark:text-white">{{ $card['value'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm dark:shadow-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Recent Users</h2>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 transition-colors dark:text-blue-400 dark:hover:text-blue-300">View All</a>
            </div>

            @if($recentUsers->isEmpty())
                <p class="text-gray-500 py-4 text-center dark:text-gray-400">No recent users found</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($recentUsers as $user)
                            <tr class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm dark:shadow-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pending Book Approvals</h2>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 transition-colors dark:text-blue-400 dark:hover:text-blue-300">Manage All</a>
            </div>

            @if($pendingApprovals->isEmpty())
                <p class="text-gray-500 py-4 text-center dark:text-gray-400">No pending approvals</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Book</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Librarian</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Date Requested</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($pendingApprovals as $approval)
                            <tr class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            📚
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $approval->book->title }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $approval->book->author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $approval->librarian->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $approval->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex space-x-2">
                                        <button class="text-green-600 hover:text-green-800 transition-colors dark:text-green-400 dark:hover:text-green-300">Approve</button>
                                        <button class="text-red-600 hover:text-red-800 transition-colors dark:text-red-400 dark:hover:text-red-300">Reject</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
