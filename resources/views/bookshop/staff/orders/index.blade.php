<x-bookshop::layouts.staff :title="'Orders - BookShop'">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">
            {{ $staff->isSuperAdmin() ? 'Orders — All Branches' : 'Orders — ' . $staff->branch?->name }}
        </h1>
        <form method="GET" class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">All Statuses</option>
                @foreach(\App\BookShop\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
            <select name="payment_status" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <option value="">Any Payment Status</option>
                @foreach(\App\BookShop\Enums\PaymentStatus::cases() as $paymentStatus)
                    <option value="{{ $paymentStatus->value }}" {{ request('payment_status') === $paymentStatus->value ? 'selected' : '' }}>{{ $paymentStatus->label() }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="text-left px-5 py-3">Order #</th>
                    <th class="text-left px-5 py-3">Customer</th>
                    @if($staff->isSuperAdmin())
                        <th class="text-left px-5 py-3">Branch</th>
                    @endif
                    <th class="text-left px-5 py-3">Total</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Payment</th>
                    <th class="text-left px-5 py-3">Placed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/40 cursor-pointer"
                        onclick="window.location='{{ route('bookshop.staff.orders.show', $order) }}'">
                        <td class="px-5 py-3 font-mono text-slate-900 dark:text-white">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $order->customer?->name }}</td>
                        @if($staff->isSuperAdmin())
                            <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $order->branch?->name }}</td>
                        @endif
                        <td class="px-5 py-3 font-mono text-slate-600 dark:text-slate-400">GHS {{ number_format($order->subtotal, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border" style="border-radius: 2px;">{{ $order->status->label() }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-semibold px-3 py-1 border {{ $order->isPaid() ? 'text-emerald-800 bg-emerald-50 border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800' : 'text-amber-800 bg-amber-50 border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800' }}" style="border-radius: 2px;">
                                {{ $order->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="bg-white dark:bg-slate-900 px-5 py-4" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            {{ $orders->links() }}
        </div>
    @endif
</x-bookshop::layouts.staff>
