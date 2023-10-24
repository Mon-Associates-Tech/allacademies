<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Support\Pricer;
use App\Models\Subscription;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use Illuminate\Support\Carbon;
use App\Models\AcademicSubject;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubscriptionRequest;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = Subscription::query()->where('team_id', auth()->user()->current_team_id)->latest('id')->paginate();

        return view('subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $academicGroups = AcademicGroup::query()
            ->with('academicLevels.academicSubjects')
            ->get()
            ->each(function (AcademicGroup $academicGroup) {
                $academicGroup->is_open = false;
                $academicGroup->academicLevels->each(function (AcademicLevel $academicLevel) {
                    $academicLevel->is_open = false;
                });
            })
            ->toArray();

        //get user teams
        $user = Auth::user();

        $user->load(['currentTeam' => ['members', 'owner']]);
        $user->currentTeam->loadCount('subscriptions');

        $ownedTeams = $user->ownedTeams()->withCount('subscriptions')->get();
        $ownedTeams->each(fn (Team $team) => $team->setRelation('owner', $user));

        $joinedTeams = $user->joinedTeams()->with('owner')->withCount('subscriptions')->get();

        $teams = $ownedTeams->merge($joinedTeams);
        unset($ownedTeams, $joinedTeams);
        $teams = $teams->sort();


        return view('subscriptions.create', [
            'academicGroups' => $academicGroups,
            'teams' => $teams,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionRequest $request)
    {

        $money = Pricer::calculate(
            $package = SubscriptionPackage::from($package = $request->input('package')),
            $duration = $request->integer('duration'),
            count($subjects = $request->validated('academic_subject_ids')),
            $beneficiaries = SubscriptionPackage::INDIVIDUAL_FULL === $package ? 1 : $request->integer('beneficiaries')
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();
        // $user->load('currentTeam');
        $subscription = new Subscription([
            'package' => $package,
            'reference' => uniqid(),
            'amount' => (string) $money->getAmount(),
            'beneficiaries' => $beneficiaries,
            'period' => $duration,
            'expires_at' => Carbon::now()->addMonths($duration),
        ]);

        $team = Team::find($request->team);

        if (
            $team->is_personal && SubscriptionPackage::INSTITUTION_FULL === $package
            || !$team->is_personal && SubscriptionPackage::INDIVIDUAL_FULL === $package
        ) {
            throw ValidationException::withMessages([
                'package' => 'You can not subscribe to this package for the current team',
            ]);
        }

        $subjectIds = $request->academic_subject_ids;
        $belongsToOneGroup = AcademicSubject::belongToOneAcademicGroup($subjectIds);

        if (!$belongsToOneGroup) {
            throw ValidationException::withMessages([
                'group' => 'Selected subjects must belong to one Academic Group',
            ]);
        }


        DB::transaction(function () use ($subscription, $user, $subjects, $team) {
            $subscription->team()->associate($team);

            $subscription = $user->subscriptions()->save($subscription);

            $subscription->academicSubjects()->attach($subjects);
        });

        return to_route('subscriptions.index')
            ->with('success', __('status.resource.created', ['name' => $subscription->reference]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        abort_unless(auth()->user()->current_team_id === $subscription->team_id, 403, 'Subscription not in your current team.');
        abort_unless(SubscriptionStatus::UNPAID === $subscription->status, 403, 'Subscription can not be deleted.');

        DB::transaction(function () use ($subscription) {
            $subscription->academicSubjects()->detach();

            $subscription->delete();
        });

        return to_route('subscriptions.index')
            ->with('success', __('status.resource.deleted', ['name' => $subscription->reference]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        $subscription->load('team')->load('academicSubjects')->loadCount('academicSubjects');
        $canRenew = false;

        if ((now()->diffInMonths($subscription->expires_at)) == 0) {
            $canRenew = true;
        }

        return view('subscriptions.show', [
            'subscription' => $subscription,
            'renew' => $canRenew,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function subjects(Subscription $subscription)
    {
        // $subject = AcademicSubject::find(4); // Replace 1 with the actual subject ID
        // $academicGroup = $subject->academicGroup;
        // dd($academicGroup);
        $subscription->load('academicSubjects');

        return view('subscriptions.subjects', [
            'subscription' => $subscription,
        ]);
    }
}
