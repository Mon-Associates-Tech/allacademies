<x-layouts.app title="Examinations" :has-action="true">
    <x-slot name="breadcrumb">
        <x-breadcrumb/>
    </x-slot>

    @if ($examinations->count())
        <!-- Subject Info Card -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ $academicSubject->name }}</h3>
                        <p class="text-xs sm:text-sm text-gray-600">
                            {{ $academicSubject->academicLevel->name }}
                            • {{ $academicSubject->academicLevel->academicGroup->name }}
                        </p>
                        <p class="text-xs sm:text-sm text-blue-600 mt-1">{{ $examinations->total() }} {{ Str::plural('examination', $examinations->total()) }}
                            available</p>
                    </div>
                </div>

                @can('privileged', $currentTeam)
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <x-link.primary
                            class="w-full sm:w-auto justify-center"
                            :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            New Examination
                        </x-link.primary>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Examinations List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach ($examinations as $examination)
                    <div class="p-3 sm:p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <!-- Examination Info -->
                            <div class="flex items-start sm:items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-900 truncate">{{ $examination->title }}</h4>
                                    <div class="flex items-center text-xs sm:text-sm text-gray-500 mt-1">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0V7a28.95 28.95 0 018 0z"></path>
                                        </svg>
                                        Created {{ $examination->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-2 sm:ml-6">
                                <a href="{{ route('examinations.show', ['examination' => $examination, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Questions
                                </a>

                                <a href="{{ route('examinations.answers', ['examination' => $examination, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Answers
                                </a>

                                <span x-data="{ format: 'none' }" class="inline-flex rounded-md">
                                    <span class="inline-flex opacity-50 items-center text-xs rounded-l-md border border-gray-300 bg-white px-2 py-1 whitespace-nowrap">
                                        Export
                                    </span>
                                    <select x-model="format" id="format-{{ $examination->id }}"
                                            @change="fileExport(format, {{$examination->id}})" name="format"
                                            class="-ml-px text-xs block w-full rounded-l-none rounded-r-md border-0 bg-white py-1 pl-3 pr-9 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset min-w-0">
                                        <option class="text-xs" value="none">None</option>
                                        <option class="text-xs" value="pdf">PDF</option>
                                        <option class="text-xs" disabled value="word">Word</option>
                                    </select>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        @if($examinations->hasPages())
            <div class="mt-6">
                {{ $examinations->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-8 sm:py-12 px-4">
            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No examinations</h3>
            <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">Get started by creating a new examination.</p>
            @can('privileged', $currentTeam)
                <div class="mt-6">
                    <x-link.primary
                        class="w-full sm:w-auto justify-center"
                        :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Examination
                    </x-link.primary>
                </div>
            @endcan
        </div>
    @endif
    <script>
        function fileExport(type = 'pdf', examination_id){
            if(type === 'none') {
                return
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
