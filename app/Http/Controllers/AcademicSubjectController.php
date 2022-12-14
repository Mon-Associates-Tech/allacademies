<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionPackage;
use Illuminate\Http\Request;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Enums\SubscriptionStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\AcademicSubjectRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;

class AcademicSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::check('moderate')) {
            $academicSubjects = AcademicSubject::query()->with('academicLevel')->get();
        } else {
            $academicSubjects = AcademicSubject::query()->with('academicLevel')->whereHas('subscriptions', function (Builder $query) {
                $query->where('status', SubscriptionStatus::PAID)
                    ->where('team_id', auth()->user()->current_team_id)
                    ->where(function (Builder $query) {
                        $query->where(function (Builder $query) {
                            $query->where('package', SubscriptionPackage::INSTITUTION_FULL)->where(function (Builder $query) {
                                $query->whereRelation('subscriber', 'id', auth()->id())->orWhereHas('team', function (Builder $query) {
                                    $query->whereRelation('members', 'user_id', auth()->id());
                                });
                            });
                        })->orWhere(function (Builder $query) {
                            $query->where('package', SubscriptionPackage::INDIVIDUAL_FULL)->whereRelation('subscriber', 'id', auth()->id());
                        });
                    })
                ;
            })->get();
        }

        return view('academic-subjects.index', [
            'academicSubjects' => $academicSubjects,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AcademicLevel $academicLevel)
    {
        $this->authorize('administrate');

        return view('academic-subjects.create', [
            'academicLevel' => $academicLevel,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AcademicLevel $academicLevel, AcademicSubjectRequest $request)
    {
        $this->authorize('administrate');

        $academicLevel->academicSubjects()->create($request->validated());

        return to_route('academic-subjects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicSubject  $academicSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicSubject $academicSubject)
    {
        $this->authorize('administrate');
    }
}
