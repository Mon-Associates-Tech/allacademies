<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AcademicGroupTag;
use App\Support\Pricer;
use App\Models\Subscription;
use App\Models\AcademicGroup;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SubscriptionRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
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
     * @return Application|Factory|\Illuminate\View\View|View
     */
    public function create()
    {
        /** @var User $user */
        $user = auth()->user();
        $user->load('currentTeam');
        $academicGroups = AcademicGroup::query()->with('academicLevels.academicSubjects')->get()->toArray();

        return view('subscriptions.create', [
            'academicGroups' => $academicGroups,
            'currentTeam' => $user->currentTeam,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SubscriptionRequest $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(SubscriptionRequest $request)
    {
        $money = Pricer::calculate(
            $package = SubscriptionPackage::from($package = $request->input('package')),
            $durationInMonths = $request->integer('duration_in_months'),
            count($subjects = $request->validated('academic_subject_ids')),
            $beneficiaries =  $request->integer('beneficiaries'),
            AcademicGroupTag::BASIC
        );

        /** @var User $user */
        $user = auth()->user();
        $user->load('currentTeam');
        $subscription = new Subscription([
            'package' => $package,
            'reference' => uniqid(),
            'amount' => (string) $money->getAmount(),
            'beneficiaries' => $beneficiaries,
            'expires_at' => Carbon::now()->addMonths($durationInMonths),
        ]);

        if (
            $user->currentTeam->is_personal && SubscriptionPackage::INSTITUTION_FULL === $package
            || !$user->currentTeam->is_personal && SubscriptionPackage::INDIVIDUAL_FULL === $package
        ) {
            throw ValidationException::withMessages([
                'package' => 'You can not subscribe to this package for the current team',
            ]);
        }

        DB::transaction(function () use ($subscription, $user, $subjects) {
            $subscription->team()->associate($user->currentTeam);

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
     * @return Response
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
}
