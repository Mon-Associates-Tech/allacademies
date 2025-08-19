<?php

namespace App\Http\Controllers;

use App\Models\BookSubscription;
use App\Models\User;
use App\Support\AcademicGroupTag;
use App\Support\Pricer;
use App\Models\Subscription;
use App\Models\AcademicGroup;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SubscriptionRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|\Illuminate\View\View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
public function index()
{
    $user = auth()->user();

    $user->ensureUserHasTeam();

    // Get regular subscriptions - keep as Eloquent collection
    $regularSubscriptions = Subscription::query()
        ->where('team_id', auth()->user()->current_team_id)
        ->with(['academicSubjects', 'team', 'subscriber'])
        ->latest('id')
        ->get();

    // Get book subscriptions for current user's student - keep as Eloquent collection
    $bookSubscriptions = BookSubscription::query()
        ->where('user_id', auth()->user()->id)
        ->with(['book', 'book.author', 'book.bookCategory', 'student'])
        ->latest('id')
        ->get();

    // Create a combined collection with computed fields
    $combinedSubscriptions = collect();

    // Add regular subscriptions with additional computed fields
    foreach ($regularSubscriptions as $subscription) {
        $combinedSubscriptions->push([
            'id' => $subscription->id,
            'type' => 'regular',
            'reference' => $subscription->reference,
            'package' => $subscription->package,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency ?? 'GHS',
            'status' => $subscription->status,
            'duration_in_months' => $subscription->duration_in_months,
            'beneficiaries' => $subscription->beneficiaries,
            'expires_at' => $subscription->expires_at,
            'created_at' => $subscription->created_at,
            'updated_at' => $subscription->updated_at,
            'subjects' => $subscription->academicSubjects->pluck('name')->join(', '),
            'subject_count' => $subscription->academicSubjects->count(),
            'model' => $subscription, // Keep the actual model
        ]);
    }

    // Add book subscriptions with additional computed fields
    foreach ($bookSubscriptions as $subscription) {
        $combinedSubscriptions->push([
            'id' => $subscription->id,
            'type' => 'book',
            'reference' => $subscription->reference,
            'package' => 'Book Subscription',
            'amount' => $subscription->annual_fee,
            'currency' => 'GHS',
            'status' => $subscription->status,
            'duration_in_months' => 12,
            'beneficiaries' => 1,
            'expires_at' => $subscription->end_date,
            'created_at' => $subscription->created_at,
            'updated_at' => $subscription->updated_at,
            'book_title' => $subscription->book->title,
            'book_author' => $subscription->book->author->user->name ?? 'Unknown Author',
            'book_category' => $subscription->book->bookCategory->name ?? 'Uncategorized',
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
            'payment_completed_at' => $subscription->payment_completed_at,
            'model' => $subscription, // Keep the actual model
        ]);
    }

    // Sort by created_at descending
    $combinedSubscriptions = $combinedSubscriptions->sortByDesc('created_at')->values();

    // Paginate the combined results
    $currentPage = request()->get('page', 1);
    $perPage = 15;

    // Create a custom paginator with the actual models
    $paginatedSubscriptions = new \Illuminate\Pagination\LengthAwarePaginator(
        $combinedSubscriptions->forPage($currentPage, $perPage),
        $combinedSubscriptions->count(),
        $perPage,
        $currentPage,
        [
            'path' => request()->url(),
            'pageName' => 'page'
        ]
    );

    return view('subscriptions.index', [
        'subscriptions' => $paginatedSubscriptions,
        'totalSubscriptions' => $combinedSubscriptions->count(),
        'regularSubscriptions' => $regularSubscriptions,
        'bookSubscriptions' => $bookSubscriptions,
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
        // Make sure academic groups are loaded with their levels and subjects
        $academicGroups = AcademicGroup::with([
            'academicLevels.academicSubjects' // or 'academicLevels.subjects' based on your relationship
        ])->get()->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'academic_levels' => $group->academicLevels->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'name' => $level->name,
                        'academic_subjects' => $level->academicSubjects->map(function ($subject) {
                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                                'code' => $subject->code,
                                // add other subject properties as needed
                            ];
                        })->toArray()
                    ];
                })->toArray()
            ];
        })->toArray();

        if (!$user->currentTeam){
            return redirect()->route('teams.create')->with('error', 'Please create a team before creating a subscription.');
        }

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
     * @throws Throwable
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    public function store(SubscriptionRequest $request): RedirectResponse
    {

        $money = Pricer::calculate(
            $package = SubscriptionPackage::from($request->input('package')),
            $durationInMonths = $request->integer('duration_in_months'),
            count($subjects = $request->validated('academic_subject_ids')),
            $beneficiaries = max($request->integer('beneficiaries') ?: 1, 1), // Ensure minimum of 1
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
            'duration_in_months' => $durationInMonths,
            'expires_at' => null, // now()->addMonths($durationInMonths)->toDateTimeString(),
        ]);

        if (
            ($user->currentTeam->is_personal && SubscriptionPackage::INSTITUTION_FULL === $package)
            || (!$user->currentTeam->is_personal && SubscriptionPackage::INDIVIDUAL_FULL === $package)
        ) {
            throw ValidationException::withMessages([
                'package' => 'You can not subscribe to this package for the current team',
            ]);
        }

        DB::transaction(static function () use ($subscription, $user, $subjects) {
            $subscription->team()->associate($user->currentTeam);

            $subscription = $user->subscriptions()->save($subscription);

            $subscription->academicSubjects()->attach($subjects);
        });

        return to_route('subscriptions.index')
            ->with('success', __('status.resource.created', ['name' => $subscription->reference]));
    }

    /**
     * Display the specified resource.
     *
     * @param Subscription $subscription
     * @return Application|Factory|View|\Illuminate\View\View
     */
    public function show(Subscription $subscription)
    {
        Gate::allowIf(static fn ($user) => $user->current_team_id === $subscription->team_id);

        $subscription->load([
            'academicSubjects.academicLevel.academicGroup',
            'team',
            'subscriber'
        ]);

        return view('subscriptions.show', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subscription $subscription
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        Gate::allowIf(static fn ($user) => $user->current_team_id === $subscription->team_id);
        Gate::allowIf(SubscriptionStatus::UNPAID === $subscription->status);

        DB::transaction(static function () use ($subscription) {
            $subscription->academicSubjects()->detach();

            $subscription->delete();
        });

        return to_route('subscriptions.index')
            ->with('success', __('status.resource.deleted', ['name' => $subscription->reference]));
    }
}
