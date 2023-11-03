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
use Illuminate\Support\Facades\Gate;
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
        $subscriptions = Subscription::query()
            ->where('team_id', auth()->user()->current_team_id)->latest('id')
            ->paginate();

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
                $academicGroup->academicLevels->each(function (AcademicLevel $academicLevel) {
                    $academicLevel->is_open = false;
                });
            })
            ->toArray();


        //get user teams
        $teams = Team::getUserTeams();

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
        $subscription = new Subscription([
            'package' => $package,
            'reference' => uniqid(),
            'amount' => (string) $money->getAmount(),
            'beneficiaries' => $beneficiaries,
            'duration' => $duration,
            'expires_at' => Carbon::now()->addMonths($duration),
        ]);

        $team = Team::find($request->team);

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
        Gate::allowIf(fn ($user) => $user->current_team_id === $subscription->team_id);
        Gate::allowIf(SubscriptionStatus::UNPAID === $subscription->status);

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
        $canRenew = now()->diffInMonths($subscription->expires_at) == 0 && $subscription->status === SubscriptionStatus::PAID ? true : false;


        return view('subscriptions.show', [
            'subscription' => $subscription,
            'canRenew' => $canRenew,
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
        $subscription->load('academicSubjects');

        return view('subscriptions.subjects', [
            'subscription' => $subscription,
        ]);
    }


    /**
     * Renew subscription
     * Create a new record with new subscription id, refrence and expires_at
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function renew(Subscription $subscription)
    {
        $newReference = uniqid();

        $newSubscription = new Subscription([
            'package' => $subscription->package,
            'reference' => $newReference,
            'amount' => $subscription->amount,
            'beneficiaries' => $subscription->beneficiaries,
            'duration' => $subscription->duration,
            'expires_at' => $subscription->expires_at->addMonths($subscription->duration),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $team = $subscription->team;

        DB::transaction(function () use ($newSubscription, $user, $subscription, $team) {
            $newSubscription->team()->associate($team);
            $user->subscriptions()->save($newSubscription);
            $newSubscription->academicSubjects()->attach($subscription->academicSubjects->pluck('id')->toArray());
        });

        return redirect()->route('subscriptions.index')
            ->with('success', __('status.subscription.renewed', [
                'reference' => $newReference
            ]));
    }
}
