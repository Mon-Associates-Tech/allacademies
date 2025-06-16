<x-layouts.app title="Examinations" :has-action="true" a>
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    @can('privileged', $currentTeam)
        <x-slot name="action">
            <x-link.primary :to="route('examinations.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">New Examination</x-link.primary>
        </x-slot>
    @endcan

    @if ($examinations->count())
        <div class="max mx-auto">
            <x-table class="">
                <x-slot name="head">
                    <tr>
                        <x-table.th>Title</x-table.th>
                        <x-table.th><span class="sr-only">Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($examinations as $examination)
                    <tr>
                        <x-table.td bold>{{ $examination->title }}</x-table.td>
                        <x-table.td action>
                            <a class="text-primary-600 hover:text-primary-900"
                               href="{{ route('examinations.show', ['examination' => $examination, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}">Question Paper</a>
                            <a class="text-primary-600 hover:text-primary-900"
                               href="{{ route('examinations.answers', ['examination' => $examination, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level'=>getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}">Answer Scheme</a>


                            <span x-data="{ format: 'none' }" class="inline-flex rounded-md">
                <span class="inline-flex opacity-50 items-center text-xs rounded-l-md border border-gray-300 bg-white px-2 py-1 sm:text-xs sm:leading-6">
                    Export
                </span>
                <select x-model="format" id="format"
                        @change="fileExport(format, {{$examination->id}})" name="format" class="-ml-px text-xs block w-full rounded-l-none rounded-r-md border-0 bg-white py-1 pl-3 pr-9 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset sm:text-xs sm:leading-6">
                    <option class="text-xs" value="none">None</option>
                    <option class="text-xs" value="pdf">PDF</option>
                    <option class="text-xs" disabled  value="word">Word</option>
                </select>
            </span>

                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
        </div>


        <div class="mt-3">
            {{ $examinations->links() }}
        </div>
    @else
        <x-blank />
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
