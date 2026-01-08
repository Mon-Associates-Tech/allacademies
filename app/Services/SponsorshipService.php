<?php

namespace App\Services;

use App\Models\SponsorshipBeneficiary;
use App\Models\SponsorshipBid;
use App\Models\SponsorshipContribution;
use App\Models\SponsorshipOffer;
use App\Models\SponsorshipProject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SponsorshipService
{
    protected PaystackService $paystack;

    protected PaymentSetupService $paymentSetup;

    public function __construct(PaystackService $paystack, PaymentSetupService $paymentSetup)
    {
        $this->paystack = $paystack;
        $this->paymentSetup = $paymentSetup;
    }

    /**
     * Create a new sponsorships project
     */
    public function createProject(User $user, array $data): SponsorshipProject
    {
        return DB::transaction(function () use ($user, $data) {
            $project = SponsorshipProject::create([
                'user_id' => $user->id,
                'school_id' => $data['school_id'] ?? null,
                'type' => $data['type'] ?? SponsorshipProject::TYPE_PROJECT,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'affected_individuals' => $data['affected_individuals'] ?? null,
                'amount_goal' => $data['amount_goal'] ?? 0,
                'deadline' => $data['deadline'] ?? null,
                'status' => SponsorshipProject::STATUS_DRAFT,
                'metadata' => $data['metadata'] ?? null,
                'images' => $data['images'] ?? [],
                'videos' => $data['videos'] ?? [],
            ]);

            // Add beneficiaries if provided
            if (!empty($data['beneficiaries'])) {
                foreach ($data['beneficiaries'] as $beneficiary) {
                    $this->addBeneficiary($project, $beneficiary);
                }
            }

            return $project;
        });
    }

    /**
     * Add a beneficiary to a project
     */
    public function addBeneficiary(SponsorshipProject $project, array $data): SponsorshipBeneficiary
    {
        return $project->beneficiaries()->create([
            'beneficiary_type' => $data['beneficiary_type'] ?? SponsorshipBeneficiary::TYPE_INDIVIDUAL,
            'student_id' => $data['student_id'] ?? null,
            'beneficiary_name' => $data['beneficiary_name'],
            'beneficiary_email' => $data['beneficiary_email'] ?? null,
            'beneficiary_phone' => $data['beneficiary_phone'] ?? null,
            'beneficiary_description' => $data['beneficiary_description'] ?? null,
            'beneficiary_details' => $data['beneficiary_details'] ?? null,
        ]);
    }

    /**
     * Update a sponsorships project
     */
    public function updateProject(SponsorshipProject $project, array $data): SponsorshipProject
    {
        $project->update([
            'name' => $data['name'] ?? $project->name,
            'type' => $data['type'] ?? $project->type,
            'description' => $data['description'] ?? $project->description,
            'affected_individuals' => $data['affected_individuals'] ?? $project->affected_individuals,
            'amount_goal' => $data['amount_goal'] ?? $project->amount_goal,
            'deadline' => $data['deadline'] ?? $project->deadline,
            'metadata' => $data['metadata'] ?? $project->metadata,
            'images' => $data['images'] ?? $project->images,
            'videos' => $data['videos'] ?? $project->videos,
        ]);

        return $project->fresh();
    }

    /**
     * Submit a project for verification
     */
    public function submitForVerification(SponsorshipProject $project): bool
    {
        if ($project->status !== SponsorshipProject::STATUS_DRAFT) {
            return false;
        }

        // Validate project has required fields
        if (empty($project->name) || $project->amount_goal <= 0) {
            return false;
        }

        return $project->submitForVerification();
    }

    /**
     * Verify/approve a project
     */
    public function verifyProject(SponsorshipProject $project, User $verifier): bool
    {
        // Check if verifier has permission (owner or reviewer role)
        if (!$verifier->hasRole('owner') && !$verifier->hasRole('reviewer')) {
            return false;
        }

        return $project->verify($verifier);
    }

    /**
     * Reject a project
     */
    public function rejectProject(SponsorshipProject $project, User $verifier, string $reason): bool
    {
        if (!$verifier->hasRole('owner') && !$verifier->hasRole('reviewer')) {
            return false;
        }

        return $project->reject($verifier, $reason);
    }

    /**
     * Create a sponsor offer
     */
    public function createSponsorshipOffer(User $sponsor, array $data): SponsorshipOffer
    {
        return SponsorshipOffer::create([
            'user_id' => $sponsor->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amount_offered' => $data['amount_offered'] ?? 0,
            'criteria' => $data['criteria'] ?? null,
            'status' => SponsorshipOffer::STATUS_OPEN,
            'accepts_bids' => $data['accepts_bids'] ?? true,
            'expires_at' => $data['expires_at'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Update a sponsor offer
     */
    public function updateSponsorshipOffer(SponsorshipOffer $offer, array $data): SponsorshipOffer
    {
        $offer->update([
            'title' => $data['title'] ?? $offer->title,
            'description' => $data['description'] ?? $offer->description,
            'amount_offered' => $data['amount_offered'] ?? $offer->amount_offered,
            'criteria' => $data['criteria'] ?? $offer->criteria,
            'accepts_bids' => $data['accepts_bids'] ?? $offer->accepts_bids,
            'expires_at' => $data['expires_at'] ?? $offer->expires_at,
            'metadata' => $data['metadata'] ?? $offer->metadata,
        ]);

        return $offer->fresh();
    }

    /**
     * Submit a bid from a benefactor to a sponsor offer
     */
    public function submitBid(SponsorshipOffer $offer, SponsorshipProject $project, User $bidder, ?string $message = null): ?SponsorshipBid
    {
        // Check if offer accepts bids
        if (!$offer->canAcceptBids()) {
            return null;
        }

        // Check if project is active
        if (!$project->isActive()) {
            return null;
        }

        // Check if bid already exists
        $existingBid = SponsorshipBid::where('sponsorship_offer_id', $offer->id)
            ->where('sponsorship_project_id', $project->id)
            ->where('user_id', $bidder->id)
            ->first();

        if ($existingBid) {
            return null;
        }

        return SponsorshipBid::create([
            'sponsorship_offer_id' => $offer->id,
            'sponsorship_project_id' => $project->id,
            'user_id' => $bidder->id,
            'message' => $message,
            'status' => SponsorshipBid::STATUS_PENDING,
        ]);
    }

    /**
     * Accept a bid
     */
    public function acceptBid(SponsorshipBid $bid, User $sponsor): bool
    {
        // Verify the sponsor owns the offer
        if ($bid->sponsorOffer->user_id !== $sponsor->id) {
            return false;
        }

        return $bid->accept();
    }

    /**
     * Reject a bid
     */
    public function rejectBid(SponsorshipBid $bid, User $sponsor, ?string $reason = null): bool
    {
        if ($bid->sponsorOffer->user_id !== $sponsor->id) {
            return false;
        }

        return $bid->reject($reason);
    }

    /**
     * Initialize a contribution payment
     */
    public function initializeContribution(array $data): ?SponsorshipContribution
    {
        $amount = $data['amount'];
        $sponsorCoversFee = $data['sponsor_covers_fee'] ?? false;

        $platformFee = SponsorshipContribution::calculatePlatformFee($amount);
        $netAmount = SponsorshipContribution::calculateNetAmount($amount, $sponsorCoversFee);
        $totalCharged = SponsorshipContribution::calculateTotalCharged($amount, $sponsorCoversFee);

        $reference = 'SPC-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

        return DB::transaction(function () use ($data, $amount, $platformFee, $netAmount, $totalCharged, $sponsorCoversFee, $reference) {
            $contribution = SponsorshipContribution::create([
                'sponsorship_project_id' => $data['sponsorship_project_id'] ?? null,
                'sponsorship_offer_id' => $data['sponsorship_offer_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'payer_name' => $data['payer_name'] ?? null,
                'payer_email' => $data['payer_email'],
                'payer_phone' => $data['payer_phone'] ?? null,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'sponsor_covers_fee' => $sponsorCoversFee,
                'total_charged' => $totalCharged,
                'net_amount' => $netAmount,
                'currency' => $data['currency'] ?? 'GHS',
                'status' => SponsorshipContribution::STATUS_PENDING,
                'payment_reference' => $reference,
                'metadata' => $data['metadata'] ?? null,
            ]);

            return $contribution;
        });
    }

    /**
     * Process payment through Paystack
     */
    public function processPayment(SponsorshipContribution $contribution, string $callbackUrl): ?array
    {
        try {
            $paymentData = [
                'email' => $contribution->payer_email,
                'amount' => $contribution->total_charged * 100, // Convert to pesewas
                'currency' => $contribution->currency,
                'reference' => $contribution->payment_reference,
                'callback_url' => $callbackUrl,
                'metadata' => [
                    'contribution_id' => $contribution->id,
                    'sponsorship_project_id' => $contribution->sponsorship_project_id,
                    'sponsorship_offer_id' => $contribution->sponsorship_offer_id,
                    'platform_fee' => $contribution->platform_fee,
                    'net_amount' => $contribution->net_amount,
                ],
            ];

            // Add subaccount if benefactor has one set up
            if ($contribution->sponsorship_project_id) {
                $project = $contribution->sponsorshipProject;
                if ($project && $project->user && $this->paymentSetup->hasValidSubaccount($project->user)) {
                    $subaccount = $this->paymentSetup->getSubaccount($project->user);
                    $paymentData['subaccount'] = $subaccount->subaccount_code;
                    $paymentData['bearer'] = 'account';
                }
            }

            $response = $this->paystack->initializeTransaction($paymentData);

            if (empty($response['status']) || !$response['status']) {
                Log::error('Paystack payment initialization failed', [
                    'contribution_id' => $contribution->id,
                    'response' => $response,
                ]);

                return null;
            }

            $contribution->update([
                'authorization_url' => $response['data']['authorization_url'],
                'transaction_id' => $response['data']['reference'],
                'paystack_response' => $response['data'],
            ]);

            return [
                'authorization_url' => $response['data']['authorization_url'],
                'reference' => $response['data']['reference'],
            ];

        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify and complete a payment
     */
    public function verifyPayment(string $reference): ?SponsorshipContribution
    {
        try {
            $response = $this->paystack->verifyTransaction($reference);

            if (empty($response['status']) || !$response['status']) {
                return null;
            }

            $contribution = SponsorshipContribution::where('payment_reference', $reference)->first();

            if (!$contribution) {
                return null;
            }

            if ($response['data']['status'] === 'success') {
                $contribution->markAsCompleted($response['data']);
            } else {
                $contribution->markAsFailed($response['data']);
            }

            return $contribution->fresh();

        } catch (\Exception $e) {
            Log::error('Payment verification error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get projects pending verification
     */
    public function getPendingVerificationProjects()
    {
        return SponsorshipProject::pendingVerification()
            ->with(['user', 'beneficiaries'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get active projects
     */
    public function getActiveProjects()
    {
        return SponsorshipProject::active()
            ->with(['user', 'beneficiaries'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get open sponsor offers
     */
    public function getOpenOffers()
    {
        return SponsorshipOffer::open()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's projects (as benefactor)
     */
    public function getUserProjects(User $user)
    {
        return SponsorshipProject::where('user_id', $user->id)
            ->with(['beneficiaries', 'contributions'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's offers (as sponsor)
     */
    public function getUserOffers(User $user)
    {
        return SponsorshipOffer::where('user_id', $user->id)
            ->with(['bids.sponsorshipProjects', 'contributions'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's contributions
     */
    public function getUserContributions(User $user)
    {
        return SponsorshipContribution::where('user_id', $user->id)
            ->with(['sponsorshipProject', 'sponsorshipOffer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
