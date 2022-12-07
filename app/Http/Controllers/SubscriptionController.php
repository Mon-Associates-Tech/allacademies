<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionPackage;
use App\Http\Requests\SubscriptionRequest;
use App\Models\AcademicSubject;
use App\Models\Subscription;
use App\Support\Pricer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Gate::check('administrate')) {
            $subscriptions = Subscription::query()->get();
        } else {
            $subscriptions = Subscription::query()->where('team_id', auth()->user()->current_team_id)->get();
        }

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
        $groups = AcademicSubject::query()
            ->select(['id', 'name', 'code', 'academic_level_id'])
            ->with('academicLevel:id,name,label')
            ->get()
            ->groupBy('academic_level_id')
            ->values()
            ->toArray();

        return view('subscriptions.create', [
            'groups' => $groups,
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
            SubscriptionPackage::from($package = $request->input('package')),
            $duration = $request->integer('duration'),
            count($subjects = $request->validated('subjects')),
            $beneficiaries = $request->integer('beneficiaries', 1)
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('currentTeam');

        DB::transaction(function () use ($user, $package, $money, $beneficiaries, $duration, $subjects) {
            $subscription = new Subscription([
                'package' => $package,
                'reference' => uniqid(),
                'amount' => (string) $money->getAmount(),
                'beneficiaries' => $beneficiaries,
                'expires_at' => Carbon::now()->addMonths($duration),
            ]);

            $subscription->team()->associate($user->currentTeam);

            $subscription = $user->subscriptions()->save($subscription);

            $subscription->academicSubjects()->attach($subjects);
        });

        return to_route('subscriptions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
