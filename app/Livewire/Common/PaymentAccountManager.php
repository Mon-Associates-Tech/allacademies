<?php

namespace App\Livewire\Common;

use App\Constants\GhanaBanks;
use App\Services\PaystackService;
use Livewire\Component;
use Exception;

class PaymentAccountManager extends Component
{
    // Public properties for binding
    public $model; // The model that has a subaccount (School or Author)
    public $modelType; // 'school' or 'author'

    // Modal state
    public $showAccountModal = false;
    public $editingAccountId = null;

    // Form fields
    public $accountBank = '';
    public $accountBankCode = '';
    public $accountNumber = '';
    public $accountName = '';

    // Validation rules
    protected $rules = [
        'accountBankCode' => 'required|string',
        'accountNumber' => 'required|string',
        'accountName' => 'nullable|string|max:255',
    ];

    protected $messages = [
        'accountBankCode.required' => 'Please select a bank.',
        'accountNumber.required' => 'Account number is required.',
    ];

    public function mount($model, $modelType = 'school')
    {
        // Validate that the model has a subaccount relationship
        if (!method_exists($model, 'subaccount')) {
            throw new \Exception("Model " . get_class($model) . " does not have a subaccount relationship");
        }

        $this->model = $model;
        $this->modelType = $modelType;
        $this->loadAccountInformation();
    }

    public function saveAccount(): void
    {
        $this->validate();

        try {
            // Get bank name from code
            $this->accountBank = GhanaBanks::getNameFromCode($this->accountBankCode);

            $paystack = app(PaystackService::class);

            // Get email for contact - handle different model structures
            $contactEmail = $this->getContactEmail();
            $contactPhone = $this->getContactPhone();

            // Determine percentage charge based on model type
            // Authors get 90% (the platform keeps 10%), Schools get 100%
            $percentageCharge = $this->modelType === 'author' ? 10 : 0;

            if ($this->editingAccountId) {
                // Update existing subaccount
                $subaccount = $this->model->subaccount;

                $updateData = [
                    'business_name' => $this->accountName ?: $this->model->name,
                    'account_number' => $this->accountNumber,
                ];

                $response = $paystack->updateSubAccount($subaccount->subaccount_code, $updateData);

                if (isset($response['status']) && $response['status']) {
                    $subaccount->update([
                        'business_name' => $this->accountName ?: $this->model->name,
                        'settlement_bank' => $this->accountBank,
                        'bank_code' => $this->accountBankCode,
                        'account_number' => $this->accountNumber,
                        'paystack_response' => $response['data'] ?? null,
                    ]);

                    session()->flash('success', 'Account information updated successfully!');
                } else {
                    throw new Exception($response['message'] ?? 'Failed to update account');
                }
            } else {
                // Create new subaccount
                $subaccountData = [
                    'business_name' => $this->accountName ?: $this->model->name,
                    'bank_code' => $this->accountBankCode,
                    'account_number' => $this->accountNumber,
                    'percentage_charge' => $percentageCharge,
                    'description' => "Payment account for {$this->model->name}",
                    'primary_contact_name' => $this->model->name,
                    'primary_contact_email' => $contactEmail,
                    'primary_contact_phone' => $contactPhone,
                ];

                $response = $paystack->createSubAccount($subaccountData);

                if (isset($response['status']) && $response['status']) {
                    $this->model->subaccount()->create([
                        'subaccount_code' => $response['data']['subaccount_code'],
                        'business_name' => $this->accountName ?: $this->model->name,
                        'settlement_bank' => $this->accountBank,
                        'bank_code' => $this->accountBankCode,
                        'account_number' => $this->accountNumber,
                        'percentage_charge' => $percentageCharge,
                        'description' => $response['data']['description'] ?? null,
                        'paystack_response' => $response['data'],
                    ]);

                    session()->flash('success', 'Account information added successfully!');
                } else {
                    throw new Exception($response['message'] ?? 'Failed to create account');
                }
            }

            $this->showAccountModal = false;
            $this->resetAccountForm();
            $this->loadAccountInformation();
            $this->dispatch('accountUpdated');
        } catch (Exception $e) {
            $errorMessage = 'Failed to save account: ' . $e->getMessage();
            session()->flash('error', $errorMessage);
        }
    }

    /**
     * Get contact email from model
     */
    private function getContactEmail(): string
    {
        // Try direct email property
        if (isset($this->model->email)) {
            return $this->model->email;
        }

        // Try through user relationship
        if (isset($this->model->user) && isset($this->model->user->email)) {
            return $this->model->user->email;
        }

        return '';
    }

    /**
     * Get contact phone from model
     */
    private function getContactPhone(): string
    {
        // Try direct phone property
        if (isset($this->model->phone)) {
            return $this->model->phone;
        }

        // Try through user relationship
        if (isset($this->model->user) && isset($this->model->user->phone)) {
            return $this->model->user->phone;
        }

        return '';
    }
    public function loadAccountInformation(): void
    {
        $subaccount = $this->model->subaccount;

        if ($subaccount) {
            $this->accountBank = $subaccount->settlement_bank ?? '';
            $this->accountBankCode = $subaccount->bank_code ?? '';
            $this->accountNumber = $subaccount->account_number ?? '';
            $this->accountName = $subaccount->business_name ?? '';
        }
    }

    public function createAccount(): void
    {
        $this->resetAccountForm();
        $this->showAccountModal = true;
    }

    public function editAccount(): void
    {
        $subaccount = $this->model->subaccount;

        if ($subaccount) {
            $this->editingAccountId = $subaccount->id;
            $this->accountBank = $subaccount->settlement_bank ?? '';
            $this->accountBankCode = $subaccount->bank_code ?? '';
            $this->accountNumber = $subaccount->account_number ?? '';
            $this->accountName = $subaccount->business_name ?? $this->model->name;
            $this->showAccountModal = true;
        }
    }

    public function deleteAccount(): void
    {
        try {
            $subaccount = $this->model->subaccount;

            if ($subaccount) {
                $subaccount->delete();
                session()->flash('success', 'Account information removed successfully!');
                $this->loadAccountInformation();
                $this->dispatch('accountDeleted');
            }
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete account: ' . $e->getMessage());
        }
    }

    private function resetAccountForm(): void
    {
        $this->accountBank = '';
        $this->accountBankCode = '';
        $this->accountNumber = '';
        $this->accountName = '';
        $this->editingAccountId = null;
        $this->resetErrorBag();
    }

    public function getBanksProperty(): array
    {
        return GhanaBanks::all();
    }

    public function render()
    {
        return view('livewire.common.payment-account-manager');
    }
}
