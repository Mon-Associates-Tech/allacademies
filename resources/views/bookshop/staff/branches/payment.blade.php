<x-bookshop::layouts.staff :title="'Payment Setup - ' . $branch->name">
    <h1 class="text-xl font-bold text-slate-900 dark:text-white" style="font-family: 'Georgia', serif;">Payment Setup — {{ $branch->name }}</h1>

    @if($branch->paymentAccount?->isReadyForPayments())
        <div class="px-4 py-3 text-sm text-emerald-800 bg-emerald-50 border border-emerald-200 dark:text-emerald-200 dark:bg-emerald-900/30 dark:border-emerald-800 flex items-center justify-between gap-3" style="border-radius: 2px;">
            <span>Active — subaccount <code class="font-mono">{{ $branch->paymentAccount->subaccount_code }}</code>. Orders from this branch settle {{ 100 - $branch->paymentAccount->percentage_charge }}% here, {{ $branch->paymentAccount->percentage_charge }}% to the platform.</span>
            <form method="POST" action="{{ route('bookshop.staff.branches.payment.deactivate', $branch) }}">
                @csrf @method('PATCH')
                <button type="submit" class="flex-shrink-0 text-xs font-semibold px-3 py-1.5 border border-emerald-300 dark:border-emerald-700" style="border-radius: 2px;" onclick="return confirm('Deactivate payments for this branch? Orders will settle to the platform account instead.')">
                    Deactivate
                </button>
            </form>
        </div>
    @elseif($branch->paymentAccount)
        <div class="px-4 py-3 text-sm text-slate-600 bg-slate-50 border border-slate-200 dark:text-slate-400 dark:bg-slate-800 dark:border-slate-700" style="border-radius: 2px;">
            Deactivated — orders from this branch currently settle to the platform's main Paystack account. Save the form below to reactivate.
        </div>
    @else
        <div class="px-4 py-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800" style="border-radius: 2px;">
            Not set up yet — orders from this branch currently settle entirely to the platform's main Paystack account, with no revenue split. Customers can still order and pay; this only affects where the money lands.
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 p-6 max-w-xl" style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <form method="POST" action="{{ route('bookshop.staff.branches.payment.update', $branch) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Business / Account Name</label>
                <input type="text" name="business_name" required value="{{ old('business_name', $branch->paymentAccount?->business_name ?? $branch->name) }}"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Bank</label>
                @if(count($banks))
                    <select name="bank_code" required
                            onchange="document.getElementById('settlement_bank_name').value = this.options[this.selectedIndex].text"
                            class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                        <option value="">Select a bank</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank['code'] }}" {{ old('bank_code', $branch->paymentAccount?->bank_code) === $bank['code'] ? 'selected' : '' }}>
                                {{ $bank['name'] }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" name="bank_code" required placeholder="Paystack bank code"
                           value="{{ old('bank_code', $branch->paymentAccount?->bank_code) }}"
                           class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">Couldn't reach Paystack's bank list — enter the numeric bank code directly. Check application logs if this persists.</p>
                @endif
                <input type="hidden" id="settlement_bank_name" name="settlement_bank_name" value="{{ old('settlement_bank_name', $branch->paymentAccount?->settlement_bank) }}">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Account Number</label>
                <input type="text" name="account_number" required minlength="10" value="{{ old('account_number', $branch->paymentAccount?->account_number) }}"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Platform Fee (%)</label>
                <input type="number" name="percentage_charge" required min="0" max="100" step="0.1"
                       value="{{ old('percentage_charge', $branch->paymentAccount?->percentage_charge ?? 0) }}"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white" style="border-radius: 2px;">
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">What the platform keeps from each sale at this branch. The rest settles directly to the branch's account via Paystack.</p>
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white transition-all"
                    style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                Save Payment Account
            </button>
        </form>
    </div>
</x-bookshop::layouts.staff>
