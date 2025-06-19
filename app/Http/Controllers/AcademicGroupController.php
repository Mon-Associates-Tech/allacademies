<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\AcademicGroupRequest;
use App\Models\AcademicGroup;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(AcademicGroupRequest $request)
    {
        $this->authorize('administrate');

        $academicGroup = AcademicGroup::query()->create($request->validated());

        return to_route('academic-groups.index')
            ->with('success', __('status.resource.created', ['name' => $academicGroup->name]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return Response
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
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return Response
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return Response
     */
    public function update(AcademicGroupRequest $request, AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        $academicGroup->update($request->validated());

        return to_route('academic-groups.show', ['academic_group' =>  $academicGroup])
            ->with('success', __('status.resource.updated', ['name' => $academicGroup->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicGroup  $academicGroup
     * @return Response
     */
    public function destroy(AcademicGroup $academicGroup)
    {
        $this->authorize('administrate');

        $academicGroup->delete();

        return to_route('academic-groups.index')
            ->with('success', __('status.resource.deleted', ['name' => $academicGroup->name]));
    }
}
