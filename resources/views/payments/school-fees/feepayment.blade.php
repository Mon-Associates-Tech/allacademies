<x-layouts.app page-name="Fee Payment">

    <div class="min-h-screen bg-gray-50 py-10 relative">
        
        {{-- 🔙 Go Back Button --}}
        <div class="absolute top-6 left-6">
            <a href="{{ url('/parent/wards') }}"
               class="inline-flex items-center px-4 py-2 bg-violet-100 text-violet-700 rounded-lg font-medium hover:bg-violet-200 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7" />
                </svg>
                Go Back
            </a>
        </div>

        <div class="max-w-lg mx-auto bg-white shadow-xl rounded-2xl p-6 mt-8">
            <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
                School Fees Payment for
                <span
                    class="inline-block bg-violet-100 text-violet-800 text-lg font-semibold px-4 py-1 rounded-full shadow-sm ml-2">
                    {{ $student->user->name ?? 'Unknown Student' }}
                </span>
            </h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('feepayment.process') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-gray-700 font-medium mb-1">Total Amount to Pay</label>
                    <input type="text" value="₵{{ number_format($student->total_fees ?? 0, 2) }}" readonly
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount You Are Paying Now <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" min="1" step="0.01" placeholder="Enter amount..." required
                        oninput="if(this.value < 1) this.value = 1;"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-violet-500 focus:border-violet-500">
                </div>

                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="flex justify-center">
                    <button type="submit"
                        class="px-6 py-2 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-700 transition duration-200">
                        Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
