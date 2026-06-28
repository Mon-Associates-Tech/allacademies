<?php

namespace App\ExaminationHub\Controllers;

use App\ExaminationHub\Models\GeneralExamParticipantGroup;
use App\ExaminationHub\Models\GeneralExamParticipantGroupMember;
use App\ExaminationHub\Services\ParticipantGroupService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantGroupController extends Controller
{
    public function __construct(
        private readonly ParticipantGroupService $groupService
    ) {}

    public function index(): View
    {
        $groups = $this->groupService->getAllGroups();

        return view('examination-hub.participant-groups.index', compact('groups'));
    }

    public function create(): View
    {
        return view('examination-hub.participant-groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:general_exam_participant_groups,name'],
            'description' => ['nullable', 'string'],
        ]);

        $this->groupService->createGroup($data['name'], $data['description'] ?? null);

        return redirect()->route('examination-hub.participant-groups.index')
            ->with('success', 'Participant group created successfully.');
    }

    public function show(GeneralExamParticipantGroup $group): View
    {
        $group = $this->groupService->getGroupWithMembers($group);

        return view('examination-hub.participant-groups.show', compact('group'));
    }

    public function edit(GeneralExamParticipantGroup $group): View
    {
        return view('examination-hub.participant-groups.edit', compact('group'));
    }

    public function update(Request $request, GeneralExamParticipantGroup $group): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:general_exam_participant_groups,name,' . $group->id],
            'description' => ['nullable', 'string'],
        ]);

        $this->groupService->updateGroup($group, $data['name'], $data['description'] ?? null);

        return redirect()->route('examination-hub.participant-groups.show', $group)
            ->with('success', 'Participant group updated successfully.');
    }

    public function destroy(GeneralExamParticipantGroup $group): RedirectResponse
    {
        $this->groupService->deleteGroup($group);

        return redirect()->route('examination-hub.participant-groups.index')
            ->with('success', 'Participant group deleted successfully.');
    }

    public function importCsv(): View
    {
        return view('examination-hub.participant-groups.import');
    }

    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls,ods', 'max:20480'],
        ]);

        $result = $this->groupService->importFromCsv($request->file('csv_file'));

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['errors'][0] ?? 'Import failed.']);
        }

        $message = "{$result['imported']} participant(s) imported into {$result['groups']} group(s).";
        
        if (!empty($result['errors'])) {
            return redirect()->route('examination-hub.participant-groups.index')
                ->with('success', $message)
                ->with('warning', count($result['errors']) . ' row(s) skipped. Check details below.')
                ->with('import_errors', $result['errors']);
        }

        return redirect()->route('examination-hub.participant-groups.index')
            ->with('success', $message);
    }

    public function storeMember(Request $request, GeneralExamParticipantGroup $group): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:100'],
        ]);

        $this->groupService->addMember($group, $data['name'], $data['email'], $data['unique_code'] ?? null);

        return back()->with('success', 'Participant added to group successfully.');
    }

    public function updateMember(Request $request, GeneralExamParticipantGroup $group, GeneralExamParticipantGroupMember $member): RedirectResponse
    {
        abort_unless($member->group_id === $group->id, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'unique_code' => ['nullable', 'string', 'max:100'],
        ]);

        $this->groupService->updateMember($member, $data['name'], $data['email'], $data['unique_code'] ?? null);

        return back()->with('success', 'Participant updated successfully.');
    }

    public function destroyMember(GeneralExamParticipantGroup $group, GeneralExamParticipantGroupMember $member): RedirectResponse
    {
        abort_unless($member->group_id === $group->id, 404);

        $this->groupService->deleteMember($member);

        return back()->with('success', 'Participant removed from group.');
    }
}
