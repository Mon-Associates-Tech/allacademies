<div>
    <h1 class="text-2xl font-bold mb-6">Dashboard Overview</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Users</h3>
            <p class="text-2xl font-bold">{{ $statistics['totalUsers'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Students</h3>
            <p class="text-2xl font-bold">{{ $statistics['totalStudents'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Teachers</h3>
            <p class="text-2xl font-bold">{{ $statistics['totalTeachers'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Total Books</h3>
            <p class="text-2xl font-bold">{{ $statistics['totalBooks'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Student Groups</h3>
            <p class="text-2xl font-bold">{{ $statistics['totalGroups'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Pending Approvals</h3>
            <p class="text-2xl font-bold">{{ $statistics['pendingApprovals'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Active Borrowings</h3>
            <p class="text-2xl font-bold">{{ $statistics['activeBorrowings'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm">Active Subscriptions</h3>
            <p class="text-2xl font-bold">{{ $statistics['activeSubscriptions'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4">Recent Users</h2>
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Email</th>
                        <th class="text-left py-2">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr>
                        <td class="py-2">{{ $user->name }}</td>
                        <td class="py-2">{{ $user->email }}</td>
                        <td class="py-2">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-lg font-bold mb-4">Pending Book Approvals</h2>
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left py-2">Book</th>
                        <th class="text-left py-2">Librarian</th>
                        <th class="text-left py-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingApprovals as $approval)
                    <tr>
                        <td class="py-2">{{ $approval->book->title }}</td>
                        <td class="py-2">{{ $approval->librarian->user->name }}</td>
                        <td class="py-2">{{ $approval->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
