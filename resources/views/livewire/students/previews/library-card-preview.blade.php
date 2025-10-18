<div class="bg-gradient-to-b from-purple-50 to-blue-50 p-8 flex items-center justify-center min-h-[500px]">
    <div class="relative">
        <!-- Library Card -->
        <div class="w-96 h-56 bg-gradient-to-br from-purple-600 to-indigo-700 rounded-xl shadow-2xl overflow-hidden relative">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="books" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M5 5h10v30H5zM20 10h10v25H20z" fill="white" opacity="0.3"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#books)"/>
                </svg>
            </div>

            <div class="relative z-10 p-6 h-full flex flex-col">
                <!-- Header with Icon -->
                <div class="flex items-center justify-between border-b border-white/30 pb-3 mb-4">
                    <div>
                        <h3 class="text-white font-bold text-lg">Library Card</h3>
                        <p class="text-white/90 text-xs">{{ $previewData['student']->school->name ?? 'School Name' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                </div>

                <!-- Member Info -->
                <div class="flex-1 space-y-3">
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-semibold">Member Name</p>
                        <p class="text-white font-bold text-base">{{ $previewData['student']->user->name }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-white/70 uppercase tracking-wider font-semibold">Student ID</p>
                            <p class="text-white font-semibold text-sm">{{ $previewData['student']->student_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-white/70 uppercase tracking-wider font-semibold">Card Type</p>
                            <p class="text-white font-semibold text-sm capitalize">{{ $previewData['card_type'] }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-semibold">Academic Level</p>
                        <p class="text-white text-sm">{{ $previewData['student']->academicLevel->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-white/30 pt-3 mt-auto">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-xs text-white/90 font-semibold">Card No:</p>
                            <p class="text-white font-mono text-sm">{{ $previewData['card_number'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-white/70">Valid Until</p>
                            <p class="text-white font-semibold text-sm">{{ $previewData['expiry_date']->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Barcode -->
                <div class="absolute bottom-2 right-2 w-20 h-14 bg-white rounded flex items-center justify-center p-1">
                    <div class="flex flex-col space-y-0.5 w-full">
                        @for($i = 0; $i < 6; $i++)
                            <div class="h-1 {{ $i % 3 === 0 ? 'bg-gray-900' : 'bg-gray-600' }}"
                                 style="width: {{ rand(60, 100) }}%"></div>
                        @endfor
                        <p class="text-[6px] text-gray-600 text-center font-mono mt-1">{{ substr($previewData['card_number'], -8) }}</p>
                    </div>
                </div>
            </div>

            <!-- Card Type Badge -->
            @if($previewData['card_type'] === 'premium')
                <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-2 py-1 rounded text-xs font-bold shadow">
                    ⭐ PREMIUM
                </div>
            @endif
        </div>

        <!-- Preview Badge -->
        <div class="absolute -top-4 -right-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold shadow-lg transform rotate-12">
            PREVIEW
        </div>
    </div>
</div>
