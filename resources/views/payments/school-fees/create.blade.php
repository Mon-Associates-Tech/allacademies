<x-layouts.app page-name="Create New School onboarding">

    {{-- School list --}}

     {{-- ✅ Schools Table --}}
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">School Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subaccount Code</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schools as $school)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $school->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $school->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $school->phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ optional($school->subaccount)->subaccount_code ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(optional($school->subaccount)->subaccount_code)
                                    <form action="{{ route('schools.collectFees', $school->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow hover:bg-blue-700">
                                            Collect Fees
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm">No Subaccount</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    {{-- ✅ Form --}}
    <div class="mt-[80px]">
        <form action="{{ route('schools.store') }}" method="POST" class="space-y-6 bg-white shadow rounded-lg p-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">School Name</label>
                <input type="text" name="name" id="name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required value="{{ old('name') }}">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">School Email</label>
                <input type="email" name="email" id="email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required value="{{ old('email') }}">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" id="phone"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    value="{{ old('phone') }}">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <input type="text" name="address" id="address"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    value="{{ old('address') }}">
                @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- ✅ Bank Selection --}}
            <div>
                <label for="bank" class="block text-sm font-medium text-gray-700">Select Bank</label>
                <select id="bank" name="bank_code"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">-- Select Bank --</option>
                    <option value="030100">Absa Bank Ghana Limited</option>
                    <option value="030100">Absa Bank Ghana Ltd</option>
                    <option value="030100">Absa Bank Ghana Ltd</option>
                    <option value="280100">Access Bank</option>
                    <option value="280100">Access Bank (Ghana) Plc</option>
                    <option value="080100">ADB Bank Limited</option>
                    <option value="300341">Affinity Ghana Savings and Loans</option>
                    <option value="080100">Agricultural Development Bank Plc</option>
                    <option value="ATL">AirtelTigo</option>
                    <option value="070101">ARB Apex Bank</option>
                    <option value="210100">Bank of Africa Ghana</option>
                    <option value="210100">Bank of Africa Ghana Limited</option>
                    <option value="010100">Bank of Ghana</option>
                    <option value="300335">Best Point Savings &amp; Loans</option>
                    <option value="140100">CAL Bank Limited</option>
                    <option value="140100">CalBank PLC</option>
                    <option value="340100">Consolidated Bank Ghana Limited</option>
                    <option value="340100">Consolidated Bank Ghana Limited</option>
                    <option value="130100">Ecobank Ghana Limited</option>
                    <option value="130100">Ecobank Ghana Plc</option>
                    <option value="200100">FBNBank (Ghana) Limited</option>
                    <option value="200100">FBNBank Ghana Limited</option>
                    <option value="240100">Fidelity Bank Ghana Limited</option>
                    <option value="240100">Fidelity Bank Ghana Limited</option>
                    <option value="170100">First Atlantic Bank Limited</option>
                    <option value="170100">First Atlantic Bank Limited</option>
                    <option value="330100">First National Bank (Ghana) Limited</option>
                    <option value="330100">First National Bank Ghana Limited</option>
                    <option value="040100">GCB Bank Limited</option>
                    <option value="230100">Guaranty Trust Bank (Ghana) Limited</option>
                    <option value="230100">Guaranty Trust Bank (Ghana) Limited</option>
                    <option value="MTN">MTN</option>
                    <option value="050100">National Investment Bank Limited</option>
                    <option value="360100">OmniBSCI Bank</option>
                    <option value="360100">OmniBSIC Bank Ghana Limited</option>
                    <option value="300457">Paystack Limited</option>
                    <option value="180100">Prudential Bank Limited</option>
                    <option value="180100">Prudential Bank Limited</option>
                    <option value="110100">Republic Bank (GH) Limited</option>
                    <option value="110100">Republic Bank (Ghana) PLC</option>
                    <option value="300361">Services Integrity Savings and Loans</option>
                    <option value="090100">Société Générale Ghana Limited</option>
                    <option value="090100">Société Générale Ghana Plc</option>
                    <option value="190100">Stanbic Bank Ghana Limited</option>
                    <option value="190100">Stanbic Bank Ghana Limited</option>
                    <option value="020100">Standard Chartered Bank Ghana Limited</option>
                    <option value="020100">Standard Chartered Bank Ghana Plc</option>
                    <option value="060100">United Bank for Africa (Ghana) Limited</option>
                    <option value="060100">United Bank for Africa Ghana Limited</option>
                    <option value="100100">Universal Merchant Bank Ghana Limited</option>
                    <option value="VOD">Vodafone</option>
                    <option value="120100">Zenith Bank (Ghana) Limited</option>
                    <option value="120100">Zenith Bank Ghana</option>
                </select>
                @error('bank_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                {{-- Hidden field for bank name --}}
                <input type="hidden" name="settlement_bank" id="settlement_bank">
            </div>

            <div>
                <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                <input type="text" name="account_number" id="account_number"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    required value="{{ old('account_number') }}">
                @error('account_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <button type="submit"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-white font-medium shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create New School
                </button>
            </div>
        </form>
    </div>

    {{-- ✅ JS to populate settlement_bank --}}
    <script>
        document.getElementById('bank').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('settlement_bank').value = selectedOption.text;
        });
    </script>
</x-layouts.app>