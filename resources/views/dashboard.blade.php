<x-auth title="Dashboard" :has-action="false">
    @if ($academicSubjects->count())

    <section class="w-full h-60 mb-8 grid grid-rows-2 gap-4 md:grid md:grid-cols-[30%_68%] md:h-40">
        <div class="flex flex-col justify-between text-center items-center rounded-3xl bg-blue-300 p-4 md:h-40">
            <h3 class="text-2xl font-semibold">{{greetUser(auth()->user()->name)}}</h3>
            <p>Welcome to your Dashboard</p>
            <div class="btn bg-range drop-shadow-2xl px-4 py-2 rounded-full w-1/2 text-sm text-white md:w-full">Explore more courses</div>
        </div>
        <div class="grid grid-cols-[30%_68%] gap-4 items-center text-center bg-blue-300  rounded-3xl p-4 md:h-40">
            <div>
                Notifications
            </div>
            <div class="flex flex-col md:gap-4 pb-1 items-center text-center bg-white rounded-2xl">

                <h3>Toolbox</h3>
                <div class="flex w-full justify-between">

                    <div class="border-r w-full flex flex-col items-center">
                        <img src="{{ asset('img/deadline.gif') }}" alt="animation" class='w-10 h-10 bg-inherit'>
                        <p class="text-sm">Calendar</p>
                    </div>
                    <div class="border-r w-full flex flex-col items-center">
                        <img src="{{ asset('img/bank.gif') }}" alt="animation" class='w-10 h-10 bg-inherit'>
                        <p class="text-sm">Test Bank</p>
                    </div>
                    <div class=" w-full flex flex-col items-center">
                        <img src="{{ asset('img/history.png') }}" alt="animation" class='w-10 h-10 bg-inherit'>
                        <p class="text-sm">Recent</p>
                    </div>
                </div>
            </div>


        </div>

    </section>

    <section class="mt-10 max-w-6xl mx-auto">



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
              <x-table.th><span >Actions</span></x-table.th>
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
                    @can('privileged', $currentTeam)
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
  <div class="min-w-min max-w-md h-fit p-4 my-10 mx-auto bg-white border border-solid border-gray-400 shadow-2xl rounded-2xl text-xl">
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
</x-auth>
