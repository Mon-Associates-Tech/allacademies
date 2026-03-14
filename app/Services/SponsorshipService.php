<?php

namespace App\Services;

use App\Models\SponsorOffer;
use App\Models\SponsorshipBeneficiary;
use App\Models\SponsorshipBid;
use App\Models\SponsorshipContribution;
use App\Models\SponsorshipProgram;
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
     * Create a new sponsorship program
     */
    public function createProgram(User $user, array $data): SponsorshipProgram
    {
        return DB::transaction(function () use ($user, $data) {
            $program = SponsorshipProgram::create([
                'user_id' => $user->id,
                'school_id' => $data['school_id'] ?? null,
                'type' => $data['type'] ?? SponsorshipProgram::TYPE_PROJECT,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'affected_individuals' => $data['affected_individuals'] ?? null,
                'amount_goal' => $data['amount_goal'] ?? 0,
                'deadline' => $data['deadline'] ?? null,
                'status' => SponsorshipProgram::STATUS_DRAFT,
                'metadata' => $data['metadata'] ?? null,
            ]);

            // Add beneficiaries if provided
            if (!empty($data['beneficiaries'])) {
                foreach ($data['beneficiaries'] as $beneficiary) {
                    $this->addBeneficiary($program, $beneficiary);
                }
            }

            return $program;
        });
    }

    /**
     * Update a sponsorship program
     */
    public function updateProgram(SponsorshipProgram $program, array $data): SponsorshipProgram
    {
        $program->update([
            'name' => $data['name'] ?? $program->name,
            'type' => $data['type'] ?? $program->type,
            'description' => $data['description'] ?? $program->description,
            'affected_individuals' => $data['affected_individuals'] ?? $program->affected_individuals,
            'amount_goal' => $data['amount_goal'] ?? $program->amount_goal,
            'deadline' => $data['deadline'] ?? $program->deadline,
            'metadata' => $data['metadata'] ?? $program->metadata,
        ]);

        return $program->fresh();
    }

    /**
     * Submit a program for verification
     */
    public function submitForVerification(SponsorshipProgram $program): bool
    {
        if ($program->status !== SponsorshipProgram::STATUS_DRAFT) {
            return false;
        }

        // Validate program has required fields
        if (empty($program->name) || $program->amount_goal <= 0) {
            return false;
        }

        return $program->submitForVerification();
    }

    /**
     * Verify/approve a program
     */
    public function verifyProgram(SponsorshipProgram $program, User $verifier): bool
    {
        // Check if verifier has permission (owner or reviewer role)
        if (!$verifier->hasRole('owner') && !$verifier->hasRole('reviewer')) {
            return false;
        }

        return $program->verify($verifier);
    }

    /**
     * Reject a program
     */
    public function rejectProgram(SponsorshipProgram $program, User $verifier, string $reason): bool
    {
        if (!$verifier->hasRole('owner') && !$verifier->hasRole('reviewer')) {
            return false;
        }

        return $program->reject($verifier, $reason);
    }

    /**
     * Add a beneficiary to a program
     */
    public function addBeneficiary(SponsorshipProgram $program, array $data): SponsorshipBeneficiary
    {
        return $program->beneficiaries()->create([
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
     * Create a sponsor offer
     */
    public function createSponsorOffer(User $sponsor, array $data): SponsorOffer
    {
        return SponsorOffer::create([
            'user_id' => $sponsor->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amount_offered' => $data['amount_offered'] ?? 0,
            'criteria' => $data['criteria'] ?? null,
            'status' => SponsorOffer::STATUS_OPEN,
            'accepts_bids' => $data['accepts_bids'] ?? true,
            'expires_at' => $data['expires_at'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    /**
     * Update a sponsor offer
     */
    public function updateSponsorOffer(SponsorOffer $offer, array $data): SponsorOffer
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
    public function submitBid(SponsorOffer $offer, SponsorshipProgram $program, User $bidder, string $message = null): ?SponsorshipBid
    {
        // Check if offer accepts bids
        if (!$offer->canAcceptBids()) {
            return null;
        }

        // Check if program is active
        if (!$program->isActive()) {
            return null;
        }

        // Check if bid already exists
        $existingBid = SponsorshipBid::where('sponsor_offer_id', $offer->id)
            ->where('sponsorship_program_id', $program->id)
            ->where('user_id', $bidder->id)
            ->first();

        if ($existingBid) {
            return null;
        }

        return SponsorshipBid::create([
            'sponsor_offer_id' => $offer->id,
            'sponsorship_program_id' => $program->id,
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
    public function rejectBid(SponsorshipBid $bid, User $sponsor, string $reason = null): bool
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
                'sponsorship_program_id' => $data['sponsorship_program_id'] ?? null,
                'sponsor_offer_id' => $data['sponsor_offer_id'] ?? null,
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
                    'sponsorship_program_id' => $contribution->sponsorship_program_id,
                    'sponsor_offer_id' => $contribution->sponsor_offer_id,
                    'platform_fee' => $contribution->platform_fee,
                    'net_amount' => $contribution->net_amount,
                ],
            ];

            // Add subaccount if benefactor has one set up
            if ($contribution->sponsorship_program_id) {
                $program = $contribution->sponsorshipProgram;
                if ($program && $program->user && $this->paymentSetup->hasValidSubaccount($program->user)) {
                    $subaccount = $this->paymentSetup->getSubaccount($program->user);
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
     * Get programs pending verification
     */
    public function getPendingVerificationPrograms()
    {
        return SponsorshipProgram::pendingVerification()
            ->with(['user', 'beneficiaries'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get active programs
     */
    public function getActivePrograms()
    {
        return SponsorshipProgram::active()
            ->with(['user', 'beneficiaries'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get open sponsor offers
     */
    public function getOpenOffers()
    {
        return SponsorOffer::open()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's programs (as benefactor)
     */
    public function getUserPrograms(User $user)
    {
        return SponsorshipProgram::where('user_id', $user->id)
            ->with(['beneficiaries', 'contributions'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's offers (as sponsor)
     */
    public function getUserOffers(User $user)
    {
        return SponsorOffer::where('user_id', $user->id)
            ->with(['bids.sponsorshipProgram', 'contributions'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user's contributions
     */
    public function getUserContributions(User $user)
    {
        return SponsorshipContribution::where('user_id', $user->id)
            ->with(['sponsorshipProgram', 'sponsorOffer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
