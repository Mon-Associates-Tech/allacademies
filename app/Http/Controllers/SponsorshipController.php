<?php

namespace App\Http\Controllers;

use App\Models\SponsorshipOffer;
use App\Models\SponsorshipProject;
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
     * Display listing of active sponsorships projects
     */
    public function index(Request $request)
    {
        $query = SponsorshipProject::active()
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

        $projects = $query->orderBy('created_at', 'desc')->paginate(12);

        // Calculate stats for each project
        $projects->getCollection()->transform(function ($project) {
            $project->goal_amount = $project->amount_goal;
            $project->realized_amount = $project->amount_raised;
            $project->left_amount = $project->amount_left;
            $project->progress_percentage = $project->progress_percentage;

            return $project;
        });

        return view('sponsorships.public.index', [
            'projects' => $projects,
            'types' => SponsorshipProject::getTypes(),
            'selectedType' => $request->type,
        ]);
    }

    /**
     * Display a specific sponsorships project
     */
    public function show(SponsorshipProject $project)
    {
        // Only show active projects publicly
        if (!$project->isActive()) {
            abort(404);
        }

        $project->load(['user', 'beneficiaries', 'school', 'contributions' => function ($q) {
            $q->completed()->latest()->limit(10);
        }])->loadCount('contributions');

        return view('sponsorships.public.show', [
            'project' => $project,
        ]);
    }

    /**
     * Display listing of sponsor offers
     */
    public function offers(Request $request)
    {
        $query = SponsorshipOffer::open()
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

        return view('sponsorships.public.offers', [
            'offers' => $offers,
        ]);
    }

    /**
     * Display a specific sponsor offer
     */
    public function showOffer(SponsorshipOffer $offer)
    {
        if (!$offer->isOpen()) {
            abort(404);
        }

        $offer->load(['user', 'bids' => function ($q) {
            $q->accepted()->with('sponsorshipProject');
        }]);

        // Get active projects for bidding (if user is logged in and is a benefactor)
        $userProjects = collect();
        if (auth()->check()) {
            $userProjects = SponsorshipProject::where('user_id', auth()->id())
                ->active()
                ->get();
        }

        return view('sponsorships.public.offer-detail', [
            'offer' => $offer,
            'userProjects' => $userProjects,
        ]);
    }

    /**
     * Landing page for philanthropy section
     */
    public function landing()
    {
        $featuredProjects = SponsorshipProject::active()
            ->with(['user', 'beneficiaries'])
            ->orderBy('amount_raised', 'desc')
            ->limit(6)
            ->get();

        $featuredOffers = SponsorshipOffer::open()
            ->with('user')
            ->orderBy('amount_offered', 'desc')
            ->limit(6)
            ->get();

        $stats = [
            'total_projects' => SponsorshipProject::active()->count(),
            'total_offers' => SponsorshipOffer::open()->count(),
            'total_raised' => SponsorshipProject::active()->sum('amount_raised'),
            'total_beneficiaries' => \App\Models\SponsorshipBeneficiary::count(),
        ];

        return view('sponsorships.public.landing', [
            'featuredProjects' => $featuredProjects,
            'featuredOffers' => $featuredOffers,
            'stats' => $stats,
        ]);
    }

    /**
     * Display user's contributions
     */
    public function myContributions()
    {
        $contributions = \App\Models\SponsorshipContribution::where('user_id', auth()->id())
            ->with(['sponsorshipProject.user', 'sponsorshipProject.school'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sponsorships.contributions.index', [
            'contributions' => $contributions,
        ]);
    }
}
