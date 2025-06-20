<x-layouts.app title="Examinations" :has-action="true">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    @can('privileged', $currentTeam)
        <x-slot name="action">
            <x-link.primary :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Examination
            </x-link.primary>
        </x-slot>
    @endcan

    @if ($examinations->count())
        <!-- Subject Info Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $academicSubject->name }}</h3>
                    <p class="text-sm text-gray-600">
                        {{ $academicSubject->academicLevel->name }} • {{ $academicSubject->academicLevel->academicGroup->name }}
                    </p>
                    <p class="text-sm text-blue-600 mt-1">{{ $examinations->total() }} {{ Str::plural('examination', $examinations->total()) }} available</p>
                </div>
            </div>
        </div>

        <!-- Examinations Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($examinations as $examination)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden">
                    <!-- Header -->
                    <div class="p-6 pb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $examination->title }}</h4>
                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0V7a28.95 28.95 0 018 0z"></path>
                                    </svg>
                                    Created {{ $examination->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 pb-4">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <a href="{{ route('examinations.show', ['examination' => $examination, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Question Paper
                            </a>
                            <a href="{{ route('examinations.answers', ['examination' => $examination, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level'=>getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                               class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Answer Scheme
                            </a>
                        </div>
                    </div>

                    <!-- Export Section -->
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                        <div x-data="{ format: 'none', isExporting: false }" class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Export:</span>
                            <div class="flex items-center space-x-2">
                                <select x-model="format"
                                        @change="if(format !== 'none') { isExporting = true; fileExport(format, {{ $examination->id }}); setTimeout(() => { format = 'none'; isExporting = false; }, 2000) }"
                                        class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="none">Choose format</option>
                                    <option value="pdf">📄 PDF</option>
                                    <option value="word" disabled>📝 Word (Coming Soon)</option>
                                </select>
                                <div x-show="isExporting" x-transition class="text-blue-600">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $examinations->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No examinations yet</h3>
            <p class="text-gray-600 mb-6 max-w-sm mx-auto">Get started by creating your first examination for {{ $academicSubject->name }}.</p>
            @can('privileged', $currentTeam)
                <x-link.primary :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create First Examination
                </x-link.primary>
            @endcan
        </div>
    @endif

    <script>
        function fileExport(type = 'pdf', examination_id) {
            if(type === 'none') {
                return;
            }

            window.axios.post(`/export/${type}`, {
                examination_id: examination_id
            }, { responseType: 'blob' })
                .then(response => {
                    const blob = new Blob([response.data], { type: response.headers['content-type'] });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');

                    // Optional: set filename from headers or fallback
                    const contentDisposition = response.headers['content-disposition'];
                    let filename = 'download.pdf';
                    if (contentDisposition && contentDisposition.includes('filename=')) {
                        filename = contentDisposition.split('filename=')[1].replace(/["']/g, '');
                    }

                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Download failed', error);
                    alert('Something went wrong while exporting.');
                });
        }
    </script>
</x-layouts.app>
