<?php

namespace App\Http\Controllers;

use App\Models\SponsorOffer;
use App\Models\SponsorshipProgram;
use App\Services\SponsorshipService;
use Illuminate\Http\Request;

class SponsorshipController extends Controller
{
    protected SponsorshipService $sponsorshipService;

    public function __construct(SponsorshipService $sponsorshipService)
    {
        $this->sponsorshipService = $sponsorshipService;
    }

    /**
     * Display listing of active sponsorship programs
     */
    public function index(Request $request)
    {
        $query = SponsorshipProgram::active()
            ->with(['user', 'beneficiaries', 'school'])
            ->withCount('contributions');

        // Filter by type
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $programs = $query->orderBy('created_at', 'desc')->paginate(12);

        // Calculate stats for each program
        $programs->getCollection()->transform(function ($program) {
            $program->goal_amount = $program->amount_goal;
            $program->realized_amount = $program->amount_raised;
            $program->left_amount = $program->amount_left;
            $program->progress_percentage = $program->progress_percentage;
            return $program;
        });

        return view('sponsorship.public.index', [
            'programs' => $programs,
            'types' => SponsorshipProgram::getTypes(),
            'selectedType' => $request->type,
        ]);
    }

    /**
     * Display a specific sponsorship program
     */
    public function show(SponsorshipProgram $program)
    {
        // Only show active programs publicly
        if (!$program->isActive()) {
            abort(404);
        }

        $program->load(['user', 'beneficiaries', 'school', 'contributions' => function ($q) {
            $q->completed()->latest()->limit(10);
        }]);

        return view('sponsorship.public.show', [
            'program' => $program,
        ]);
    }

    /**
     * Display listing of sponsor offers
     */
    public function offers(Request $request)
    {
        $query = SponsorOffer::open()
            ->with('user')
            ->withCount(['bids', 'acceptedBids']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $offers = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('sponsorship.public.offers', [
            'offers' => $offers,
        ]);
    }

    /**
     * Display a specific sponsor offer
     */
    public function showOffer(SponsorOffer $offer)
    {
        if (!$offer->isOpen()) {
            abort(404);
        }

        $offer->load(['user', 'bids' => function ($q) {
            $q->accepted()->with('sponsorshipProgram');
        }]);

        // Get active programs for bidding (if user is logged in and is a benefactor)
        $userPrograms = collect();
        if (auth()->check()) {
            $userPrograms = SponsorshipProgram::where('user_id', auth()->id())
                ->active()
                ->get();
        }

        return view('sponsorship.public.offer-detail', [
            'offer' => $offer,
            'userPrograms' => $userPrograms,
        ]);
    }

    /**
     * Landing page for philanthropy section
     */
    public function landing()
    {
        $featuredPrograms = SponsorshipProgram::active()
            ->with(['user', 'beneficiaries'])
            ->orderBy('amount_raised', 'desc')
            ->limit(6)
            ->get();

        $featuredOffers = SponsorOffer::open()
            ->with('user')
            ->orderBy('amount_offered', 'desc')
            ->limit(6)
            ->get();

        $stats = [
            'total_programs' => SponsorshipProgram::active()->count(),
            'total_offers' => SponsorOffer::open()->count(),
            'total_raised' => SponsorshipProgram::active()->sum('amount_raised'),
            'total_beneficiaries' => \App\Models\SponsorshipBeneficiary::count(),
        ];

        return view('sponsorship.public.landing', [
            'featuredPrograms' => $featuredPrograms,
            'featuredOffers' => $featuredOffers,
            'stats' => $stats,
        ]);
    }
}
