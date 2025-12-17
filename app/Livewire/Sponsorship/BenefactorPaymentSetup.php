<?php

namespace App\Livewire\Sponsorship;

use App\Constants\GhanaBanks;
use App\Services\PaymentSetupService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BenefactorPaymentSetup extends Component
{
    public $business_name = '';
    public $bank_code = '';
    public $bank_name = '';
    public $account_number = '';
    public $description = '';

    public $hasSubaccount = false;
    public $existingSubaccount = null;

    protected $rules = [
        'business_name' => 'required|string|min:3|max:255',
        'bank_code' => 'required|string',
        'account_number' => 'required|string|min:10|max:20',
        'description' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $user = Auth::user();

        // Check if user already has a subaccount
        if ($user->subaccount) {
            $this->hasSubaccount = true;
            $this->existingSubaccount = $user->subaccount;
            $this->business_name = $user->subaccount->business_name;
            $this->bank_code = $user->subaccount->bank_code;
            $this->bank_name = $user->subaccount->settlement_bank;
            $this->account_number = $user->subaccount->account_number;
            $this->description = $user->subaccount->description;
        } else {
            // Pre-fill with user's name
            $this->business_name = $user->name;
        }
    }

    public function updatedBankCode($value)
    {
        $this->bank_name = GhanaBanks::getNameFromCode($value);
    }

    public function save()
    {
        $this->validate();

        $paymentSetupService = app(PaymentSetupService::class);
        $user = Auth::user();

        try {
            $bankDetails = [
                'business_name' => $this->business_name,
                'bank_code' => $this->bank_code,
                'bank_name' => $this->bank_name,
                'settlement_bank' => $this->bank_name,
                'account_number' => $this->account_number,
                'description' => $this->description,
                'percentage_charge' => 0, // Platform takes fee separately
            ];

            if ($this->hasSubaccount) {
                $subaccount = $paymentSetupService->updateSubaccount($user, $bankDetails);
                $message = 'Payment account updated successfully.';
            } else {
                $subaccount = $paymentSetupService->createSubaccount($user, $bankDetails);
                $message = 'Payment account created successfully.';
            }

            if ($subaccount) {
                $this->hasSubaccount = true;
                $this->existingSubaccount = $subaccount;
                session()->flash('message', $message);
            } else {
                session()->flash('error', 'Failed to set up payment account. Please check your details and try again.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $banks = GhanaBanks::all();

        return view('livewire.sponsorship.benefactor-payment-setup', [
            'banks' => $banks,
            'platformFeePercentage' => app(PaymentSetupService::class)->getPlatformFeePercentageDisplay(),
        ]);
    }
}
