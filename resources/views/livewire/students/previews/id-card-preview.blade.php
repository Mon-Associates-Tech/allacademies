<div class="bg-gray-100 p-8 flex items-center justify-center min-h-[500px]">
    <div class="relative">
        <!-- Front Side -->
        <div class="w-96 h-60 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-2xl p-6 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.1) 10px, rgba(255,255,255,.1) 20px);"></div>
            </div>

            <!-- Header -->
            <div class="relative z-10 text-center border-b border-white/30 pb-3 mb-4">
                <h3 class="text-white font-bold text-lg">{{ $previewData['student']->school->name ?? 'School Name' }}</h3>
                <p class="text-white/90 text-sm font-semibold mt-1">STUDENT IDENTITY CARD</p>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex gap-4">
                <!-- Photo Area -->
                <div class="w-24 h-24 bg-white/20 border-2 border-white/50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-white/70" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-white/70 mt-1">PHOTO</p>
                    </div>
                </div>

                <!-- Details -->
                <div class="flex-1 space-y-2">
                    <div>
                        <p class="text-xs text-white/80 font-semibold">Name</p>
                        <p class="text-white font-bold text-sm">{{ $previewData['student']->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-white/80 font-semibold">ID Number</p>
                        <p class="text-white font-bold text-sm">{{ $previewData['student']->student_id ?? 'N/A' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <p class="text-xs text-white/80 font-semibold">Level</p>
                            <p class="text-white text-xs">{{ $previewData['student']->academicLevel->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-white/80 font-semibold">Class</p>
                            <p class="text-white text-xs">{{ $previewData['student']->studentGroup->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="relative z-10 mt-4 pt-3 border-t border-white/30 text-center">
                <p class="text-white text-xs font-semibold">Card #: {{ $previewData['card_number'] }}</p>
                <p class="text-white/80 text-xs mt-1">
                    Valid: {{ $previewData['issue_date']->format('M Y') }} - {{ $previewData['expiry_date']->format('M Y') }}
                </p>
            </div>

            <!-- Barcode Area -->
            <div class="absolute bottom-2 right-2 w-16 h-12 bg-white/90 rounded flex items-center justify-center">
                <div class="space-y-0.5">
                    @for($i = 0; $i < 8; $i++)
                        <div class="h-0.5 {{ $i % 2 === 0 ? 'bg-gray-800' : 'bg-gray-400' }}"
                             style="width: {{ rand(20, 50) }}px"></div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Badge/Tag -->
        <div class="absolute -top-4 -right-4 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold shadow-lg transform rotate-12">
            PREVIEW
        </div>
    </div>
</div>
