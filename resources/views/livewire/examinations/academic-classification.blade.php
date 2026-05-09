<div>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select the academic hierarchy for this examination (applies to all sections)</p>
    <div class="space-y-3">
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Academic Group</label>
            @livewire('common.searchable-multi-select', [
                'items' => collect($hierarchyTree)->map(fn($g) => ['id' => $g['id'], 'name' => $g['name']])->values()->all(),
                'selected' => $academicGroupId ? [(string) $academicGroupId] : [],
                'name' => 'academic_group_id',
                'multiple' => false,
                'placeholder' => 'Select group',
            ], key('exam-group-' . ($academicGroupId ?? 'none')))
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Academic Level</label>
            @livewire('common.searchable-multi-select', [
                'items' => $this->levelItems(),
                'selected' => $academicLevelId ? [(string) $academicLevelId] : [],
                'name' => 'academic_level_id',
                'multiple' => false,
                'placeholder' => 'Select level',
            ], key('exam-level-' . ($academicLevelId ?? 'none') . '-' . ($academicGroupId ?? 'none')))
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Subject</label>
            @livewire('common.searchable-multi-select', [
                'items' => $this->subjectItems(),
                'selected' => $academicSubjectId ? [(string) $academicSubjectId] : [],
                'name' => 'academic_subject_id',
                'multiple' => false,
                'placeholder' => 'Select subject',
            ], key('exam-subject-' . ($academicSubjectId ?? 'none') . '-' . ($academicLevelId ?? 'none')))
        </div>
    </div>
</div>
