<x-auth title="Dashboard">
    @if ($academicSubjects->count())
  <x-table>
      <x-slot name="head">
          <tr>
              <x-table.th>Available Subjects</x-table.th>
              <x-table.th><span class="sr-only">Actions</span></x-table.th>
          </tr>
      </x-slot>

      @foreach ($academicSubjects as $academicSubject)
          <tr>
                <x-table.td bold>
                    <span span class="text-gray-500">{{ $academicSubject->academicLevel->academicGroup->name }}</span>
                    <span class="text-gray-500">/</span>
                    <span class="text-gray-500">{{ $academicSubject->academicLevel->name }}</span>
                    <span class="text-gray-500">/</span>
                    <span>{{ $academicSubject->name }}</span>
                </x-table.td>
                <x-table.td action>
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('academic-subjects.quizzes.index', ['academic_subject' => $academicSubject]) }}">Quizzes</a>
                    @can('privileged', Auth::user()->currentTeam)
                        <a class="text-primary-600 hover:text-primary-900" href="{{ route('academic-subjects.examinations.index', ['academic_subject' => $academicSubject]) }}">Examinations</a>
                    @endcan
                </x-table.td>
          </tr>
      @endforeach
  </x-table>

  <div class="mt-3">
      {{ $academicSubjects->links() }}
  </div>
  @else
  <a href="{{ route('subscriptions.create') }}" type="button" class="relative block w-full rounded-lg border-2 border-dashed border-gray-300 p-12 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
    </svg>

    <span class="mt-2 block text-sm font-semibold text-gray-900">No active package found. Subscribe here.</span>
  </a>

  @endif
</x-auth>
