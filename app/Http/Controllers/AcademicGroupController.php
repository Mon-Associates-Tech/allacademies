<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\AcademicGroupRequest;
use App\Models\AcademicGroup;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AcademicGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function index()
    {
        $this->authorize('moderate');

        $academicGroups = AcademicGroup::query()->latest('id')->paginate();

        return view('academic-groups.index', [
            'academicGroups' => $academicGroups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function create()
    {
        $this->authorize('administrate');

        return view('academic-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AcademicGroupRequest $request
     * @return RedirectResponse
     */
    public function store(AcademicGroupRequest $request): RedirectResponse
    {
        $this->authorize('administrate');

        $academicGroup = AcademicGroup::query()->create($request->validated());

        return to_route('academic-groups.index')
            ->with('success', __('status.resource.created', ['name' => $academicGroup->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function show(AcademicGroup $academicGroup)
    {
        $this->authorize('moderate');

        // Load additional relationships and counts for better data display
        $academicGroup->loadCount([
            'academicLevels',
            'teachers'
        ]);

        // You can also load recent academic levels if needed
        // $academicGroup->load(['academicLevels' => function($query) {
        //     $query->latest()->limit(5);
        // }]);

        return view('academic-groups.show', [
            'academicGroup' => $academicGroup,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AcademicGroup $academicGroup
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function edit(AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        return view('academic-groups.edit', [
            'academicGroup' => $academicGroup,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AcademicGroupRequest $request
     * @param AcademicGroup $academicGroup
     * @return RedirectResponse
     */
    public function update(AcademicGroupRequest $request, AcademicGroup $academicGroup): RedirectResponse
    {
        $this->authorize('administrate');

        $academicGroup->update($request->validated());

        return to_route('academic-groups.show', ['academic_group' => $academicGroup])
            ->with('success', __('status.resource.updated', ['name' => $academicGroup->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AcademicGroup $academicGroup
     * @return RedirectResponse
     */
    public function destroy(Request $request, AcademicGroup $academicGroup): RedirectResponse
    {
        $this->authorize('administrate');

        $academicGroup->delete();

        return to_route('academic-groups.index', ['page' => $request->input('page')])
            ->with('success', __('status.resource.deleted', ['name' => $academicGroup->name]));
    }
}
