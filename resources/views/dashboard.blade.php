@php use App\Enums\UserRole; @endphp
<x-layouts.app :has-action="false" page-name="Dashboard">


    <div class="grid grid-cols-12 gap-6 hidden">

        <!-- Doughnut chart (Top Countries) -->
        <x-dashboard.dashboard-card-06/>

        <!-- Table (Top Channels) -->
        <x-dashboard.dashboard-card-07/>

        <!-- Card (Customers) -->
        <x-dashboard.dashboard-card-10/>

        <!-- Card (Recent Activity) -->
        <x-dashboard.dashboard-card-12/>

        <!-- Card (Income/Expenses) -->
        <x-dashboard.dashboard-card-13/>

    </div>

    @if(Auth::user()->role === 'student' )
        @livewire('students.dashboard')
    @endif()
    @if(Auth::user()->role === 'teacher')
        @livewire('teachers.dashboard')
    @endif
    @if(Auth::user()->role === 'librarian')
        @livewire('librarians.dashboard')
    @endif
    @if(Auth::user()->role === 'admin' ||  Auth::user()->role === 'owner' || Auth::user()->role === 'moderator')
        @livewire('administrators.dashboard')
    @endif

    @if ($academicSubjects->count())
        <section class="mt-10 w-full hidden mx-auto">


            <h3 class="font-semibold text-2xl pb-4">My Courses</h3>
            <!-- <nav class="w-full mb-4 py-2">
                <ul class="flex justify-between items-center">
                    <li><a href="/">University</a></li>
                    <li><a href="/">Senior High</a></li>
                    <li><a href="/">Junior High</a></li>
                    <li><a href="/">Primary </a></li>
                    <li><a href="/">PreSchool</a></li>


                </ul>
            </nav> -->
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>Available Subjects</x-table.th>
                        <x-table.th><span>Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($academicSubjects as $academicSubject)
                    <tr>
                        <x-table.td bold>
                            <span span
                                  class="text-gray-500">{{ $academicSubject->academicLevel->academicGroup->name }}</span>
                            <span class="text-gray-500">/</span>
                            <span class="text-gray-500">{{ $academicSubject->academicLevel->name }}</span>
                            <span class="text-gray-500">/</span>
                            <span>{{ $academicSubject->name }}</span>
                        </x-table.td>
                        <x-table.td action>
                            <a class="text-primary-600 hover:text-primary-900"
                               href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}">Quizzes</a>
                            @can('privileged', $currentTeam)
                                <a class="text-primary-600 hover:text-primary-900"
                                   href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}">Examinations</a>
                            @endcan
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>

            <div class="mt-3">
                {{ $academicSubjects->links() }}
            </div>
            @else
                <div
                    class="min-w-min max-w-md h-fit p-4 my-10 mx-auto bg-white border border-solid border-gray-400 shadow-2xl rounded-2xl text-xl">
                    <div class="h-3/4 w-full mb-4 ">
                        <img src="{{ asset('img/image.gif') }}" alt="subscribe" class="rounded-lg">
                    </div>
                    <div class="content">
                        <p>It appears you do not have a subscription. Click below to join the All Academies Family</p>
                        <a href="{{ route('subscriptions.create') }}">

                            <div class="bg-blue-400 inline-block px-4 py-2 text-white rounded-full mt-2.5">
                                Join Us
                            </div>
                        </a>
                    </div>
                </div>

            @endif

        </section>
</x-layouts.app>
