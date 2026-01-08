<?php

namespace App\Http\Controllers;

use App\Models\SponsorshipContribution;
use App\Models\SponsorshipProject;
use App\Services\SponsorshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SponsorshipPaymentController extends Controller
{
    protected SponsorshipService $sponsorshipService;

    public function __construct(SponsorshipService $sponsorshipService)
    {
        $this->sponsorshipService = $sponsorshipService;
    }

    /**
     * Show the donation form for a project
     */
    public function showContributeForm(SponsorshipProject $project)
    {
        if (!$project->isActive()) {
            return redirect()->route('sponsorships.projects.index')
                ->withErrors(['error' => 'This project is not currently accepting donations.']);
        }

        $feeBreakdown = app(\App\Services\PaymentSetupService::class)->getFeeBreakdown(100, false);

        return view('sponsorship.public.donate', [
            'project' => $project,
            'platformFeePercentage' => SponsorshipContribution::getPlatformFeePercentageDisplay(),
        ]);
    }

    /**
     * Get fee breakdown via AJAX
     */
    public function getFeeBreakdown(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'sponsor_covers_fee' => 'boolean',
        ]);

        $amount = $validated['amount'];
        $sponsorCoversFee = $validated['sponsor_covers_fee'] ?? false;

        $platformFee = SponsorshipContribution::calculatePlatformFee($amount);
        $netAmount = SponsorshipContribution::calculateNetAmount($amount, $sponsorCoversFee);
        $totalCharged = SponsorshipContribution::calculateTotalCharged($amount, $sponsorCoversFee);

        return response()->json([
            'amount' => number_format($amount, 2),
            'platform_fee' => number_format($platformFee, 2),
            'platform_fee_percentage' => SponsorshipContribution::getPlatformFeePercentageDisplay(),
            'net_amount' => number_format($netAmount, 2),
            'total_charged' => number_format($totalCharged, 2),
            'sponsor_covers_fee' => $sponsorCoversFee,
            'message' => $sponsorCoversFee
                ? "You will be charged GHS {$totalCharged} (includes platform fee). The benefactor will receive GHS {$amount}."
                : "You will be charged GHS {$amount}. After the platform fee, the benefactor will receive GHS {$netAmount}.",
        ]);
    }

    /**
     * Initialize a donation payment
     */
    public function initializeContribution(Request $request)
    {
        $validated = $request->validate([
            'sponsorship_project_id' => 'required|exists:sponsorship_projects,id',
            'amount' => 'required|numeric|min:1',
            'sponsor_covers_fee' => 'boolean',
            'payer_name' => 'nullable|string|max:255',
            'payer_email' => 'required|email',
            'payer_phone' => 'nullable|string|max:20',
        ]);

        $project = SponsorshipProject::find($validated['sponsorship_project_id']);

        if (!$project || !$project->isActive()) {
            return back()->withErrors(['error' => 'This project is not currently accepting donations.']);
        }

        // Calculate fee breakdown for display
        $amount = $validated['amount'];
        $sponsorCoversFee = $validated['sponsor_covers_fee'] ?? false;
        $platformFee = SponsorshipContribution::calculatePlatformFee($amount);
        $netAmount = SponsorshipContribution::calculateNetAmount($amount, $sponsorCoversFee);
        $totalCharged = SponsorshipContribution::calculateTotalCharged($amount, $sponsorCoversFee);

        try {
            // Create contribution record
            $contribution = $this->sponsorshipService->initializeContribution([
                'sponsorship_project_id' => $project->id,
                'user_id' => auth()->id(),
                'amount' => $amount,
                'sponsor_covers_fee' => $sponsorCoversFee,
                'payer_name' => $validated['payer_name'] ?? (auth()->check() ? auth()->user()->name : 'Anonymous'),
                'payer_email' => $validated['payer_email'],
                'payer_phone' => $validated['payer_phone'] ?? null,
                'metadata' => [
                    'project_name' => $project->name,
                    'project_code' => $project->code,
                ],
            ]);

            if (!$contribution) {
                return back()->withErrors(['error' => 'Failed to initialize payment. Please try again.']);
            }

            // Process payment through Paystack
            $callbackUrl = route('sponsorships.payment.callback');
            $paymentResult = $this->sponsorshipService->processPayment($contribution, $callbackUrl);

            if (!$paymentResult) {
                return back()->withErrors(['error' => 'Failed to connect to payment gateway. Please try again.']);
            }

            return redirect($paymentResult['authorization_url']);

        } catch (\Exception $e) {
            Log::error('Sponsorship payment initialization error', [
                'error' => $e->getMessage(),
                'project_id' => $project->id,
            ]);

            return back()->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Handle payment callback from Paystack
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('sponsorships.projects.index')
                ->withErrors(['error' => 'Missing payment reference.']);
        }

        try {
            $contribution = $this->sponsorshipService->verifyPayment($reference);

            if (!$contribution) {
                return redirect()->route('sponsorships.projects.index')
                    ->withErrors(['error' => 'Payment verification failed.']);
            }

            if ($contribution->isCompleted()) {
                return redirect()->route('sponsorships.payment.success', ['contribution' => $contribution->id]);
            }

            return redirect()->route('sponsorships.projects.index')
                ->withErrors(['error' => 'Payment was not successful. Please try again.']);

        } catch (\Exception $e) {
            Log::error('Sponsorship payment callback error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('sponsorships.projects.index')
                ->withErrors(['error' => 'Error processing payment verification.']);
        }
    }

    /**
     * Show payment success page
     */
    public function success(SponsorshipContribution $contribution)
    {
        $contribution->load(['sponsorshipProject', 'user']);

        return view('sponsorships.public.success', [
            'contribution' => $contribution,
        ]);
    }

    /**
     * View contribution receipt
     */
    public function viewReceipt(SponsorshipContribution $contribution)
    {
        if ($contribution->user_id !== auth()->id()) {
            abort(403);
        }

        $contribution->load(['sponsorshipProject.user', 'sponsorshipProject.school']);

        return view('sponsorships.contributions.receipt', [
            'contribution' => $contribution,
        ]);
    }

    /**
     * Retry pending payment
     */
    public function retryPayment(SponsorshipContribution $contribution)
    {
        if ($contribution->user_id !== auth()->id()) {
            abort(403);
        }

        if ($contribution->status !== 'pending') {
            return redirect()->route('sponsorships.contributions.mine')
                ->withErrors(['error' => 'This contribution cannot be retried.']);
        }

        try {
            $callbackUrl = route('sponsorships.payment.callback');
            $paymentResult = $this->sponsorshipService->processPayment($contribution, $callbackUrl);

            if (!$paymentResult) {
                return back()->withErrors(['error' => 'Failed to connect to payment gateway. Please try again.']);
            }

            return redirect($paymentResult['authorization_url']);

        } catch (\Exception $e) {
            Log::error('Sponsorship payment retry error', [
                'contribution_id' => $contribution->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }
}
