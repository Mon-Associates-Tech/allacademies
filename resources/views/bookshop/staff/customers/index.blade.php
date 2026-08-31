<x-bookshop::layouts.staff :title="'Customers - BookShop'">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
        {{ $staff->isSuperAdmin() ? 'Customers — All Branches' : 'Customers — ' . $staff->branch?->name }}
    </h1>

    <form method="GET" class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
               class="flex-1 px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:bg-slate-800 dark:text-white"
               style="border-radius: 2px;">
        <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white" style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
            Search
        </button>
    </form>

    <form method="POST" action="{{ route('bookshop.staff.customers.send-email') }}" id="customer-email-form">
        @csrf

        <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3 w-10">
                            <input type="checkbox" id="select-all-customers" style="border-radius: 2px;">
                        </th>
                        <th class="text-left px-5 py-3">Name</th>
                        <th class="text-left px-5 py-3">Email</th>
                        <th class="text-left px-5 py-3">Location</th>
                        <th class="text-left px-5 py-3">Branch</th>
                        <th class="text-left px-5 py-3">Orders</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr class="border-t border-slate-100 dark:border-slate-800">
                            <td class="px-5 py-3">
                                <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" class="customer-checkbox" style="border-radius: 2px;">
                            </td>
                            <td class="px-5 py-3 font-semibold text-slate-900 dark:text-white">{{ $customer->name }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $customer->email }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $customer->city }}, {{ $customer->region }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $customer->preferredBranch?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $customer->orders_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="bg-white dark:bg-slate-900 px-5 py-4 mt-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                {{ $customers->links() }}
            </div>
        @endif

        @if($customers->isNotEmpty())
            <div class="mt-6 bg-white dark:bg-slate-900 p-6" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider mb-4" style="letter-spacing: 0.1em;">Email Selected Customers</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Subject</label>
                        <input type="text" name="subject" required
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Message</label>
                        <textarea name="message" rows="5" required
                                  class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;"></textarea>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);"
                            onclick="return confirmSend();">
                        Send Email
                    </button>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Sent to whichever customers are checked above, at the time you click Send.</p>
                </div>
            </div>
        @endif
    </form>

    <script>
        document.getElementById('select-all-customers')?.addEventListener('change', function () {
            document.querySelectorAll('.customer-checkbox').forEach((cb) => cb.checked = this.checked);
        });

        function confirmSend() {
            const checked = document.querySelectorAll('.customer-checkbox:checked').length;
            if (checked === 0) {
                alert('Select at least one customer first.');
                return false;
            }
            return confirm(`Send this email to ${checked} customer(s)?`);
        }
    </script>
</x-bookshop::layouts.staff>
