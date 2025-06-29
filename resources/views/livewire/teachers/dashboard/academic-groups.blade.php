<div class="space-y-6">
    <h3 class="text-lg font-medium text-gray-900">Your Academic Structure</h3>
    
    <!-- Academic Groups -->
    @foreach($teacher->academicGroups as $group)
        <div class="border rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-900">{{ $group->name }}</h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    @if($group->pivot->is_primary) bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif">
                    @if($group->pivot->is_primary) Primary @else Secondary @endif
                </span>
            </div>

            <!-- Academic Levels in this Group -->
            <div class="space-y-4">
                @foreach($group->academicLevels as $level)
                    @if($teacher->academicLevels->contains($level->id))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-medium text-gray-900">{{ $level->name }}</h5>
                                <span class="text-sm text-gray-600">
                                    {{ $level->students->count() }} students
                                </span>
                            </div>

                            <!-- Subjects for this Level -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($level->academicSubjects as $subject)
                                    @if($teacher->subjects->contains($subject->id))
                                        <div class="bg-white rounded-lg p-3 border">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h6 class="font-medium text-gray-900">{{ $subject->name }}</h6>
                                                    <p class="text-sm text-gray-600">{{ $subject->code }}</p>
                                                </div>
                                                <div class="flex space-x-1">
                                                    <a href="{{ route('teachers.assignments.create', ['subject' => $subject]) }}" 
                                                       class="text-blue-600 hover:text-blue-900 text-sm">
                                                        Quiz
                                                    </a>
                                                    <span class="text-gray-300">|</span>
                                                    <a href="{{ route('teachers.examinations.create', ['subject' => $subject]) }}" 
                                                       class="text-green-600 hover:text-green-900 text-sm">
                                                        Exam
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <!-- Topics Preview -->
                                            @if($subject->topics->count() > 0)
                                                <div class="mt-2 pt-2 border-t border-gray-100">
                                                    <p class="text-xs text-gray-500">
                                                        Topics: {{ $subject->topics->take(3)->pluck('name')->join(', ') }}
                                                        @if($subject->topics->count() > 3)
                                                            ... +{{ $subject->topics->count() - 3 }} more
                                                        @endif
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach

    @if($teacher->academicGroups->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No academic assignments</h3>
            <p class="mt-1 text-sm text-gray-500">You haven't been assigned to any academic groups yet.</p>
        </div>
    @endif
</div>
