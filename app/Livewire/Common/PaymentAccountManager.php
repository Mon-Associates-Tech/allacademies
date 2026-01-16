<?php

namespace App\Livewire\Common;

use App\Constants\GhanaBanks;
use App\Models\Subaccount;
use App\Services\SubaccountPaymentService;
use Exception;
use Livewire\Component;

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

    // Subaccounts list
    public $subaccounts = [];

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
        // Validate that the model has a subaccounts relationship
        if (! method_exists($model, 'subaccounts')) {
            throw new \Exception('Model '.get_class($model).' does not have a subaccounts relationship');
        }

        $this->model = $model;
        $this->modelType = $modelType;
        $this->loadSubaccounts();
    }

    /**
     * Load all subaccounts for the model
     */
    public function loadSubaccounts(): void
    {
        $this->subaccounts = $this->model->activeSubaccounts()
            ->get()
            ->map(function (Subaccount $subaccount) {
                return [
                    'id' => $subaccount->id,
                    'name' => $subaccount->name,
                    'business_name' => $subaccount->business_name,
                    'bank' => $subaccount->settlement_bank,
                    'bank_code' => $subaccount->bank_code,
                    'account_number' => $subaccount->account_number,
                    'is_primary' => $subaccount->is_primary,
                    'status' => $subaccount->status,
                    'subaccount_code' => $subaccount->subaccount_code,
                ];
            })
            ->toArray();
    }

    public function saveAccount(): void
    {
        $this->validate();

        try {
            // Get bank name from code
            $this->accountBank = GhanaBanks::getNameFromCode($this->accountBankCode);

            $subaccountService = app(SubaccountPaymentService::class);

            // Get email for contact - handle different model structures
            $contactEmail = $this->getContactEmail();
            $contactPhone = $this->getContactPhone();

            // Determine percentage charge based on model type
            // Authors get 10% (the platform keeps 10%), Schools get 0%
            $percentageCharge = $this->modelType === 'author' ? 10 : 0;

            if ($this->editingAccountId) {
                // Update existing subaccount
                $subaccount = Subaccount::find($this->editingAccountId);

                if (! $subaccount || $subaccount->subaccountable_id !== $this->model->id) {
                    session()->flash('error', 'Subaccount not found or does not belong to this model.');

                    return;
                }

                $bankData = [
                    'business_name' => $this->accountName ?: $this->model->name,
                    'bank_code' => $this->accountBankCode,
                    'account_number' => $this->accountNumber,
                    'settlement_bank' => $this->accountBank,
                ];

                $subaccountService->updateSubAccount($subaccount, $bankData);
                session()->flash('success', 'Account information updated successfully!');
            } else {
                // Create new subaccount
                $bankData = [
                    'business_name' => $this->accountName ?: $this->model->name,
                    'bank_code' => $this->accountBankCode,
                    'account_number' => $this->accountNumber,
                    'settlement_bank' => $this->accountBank,
                    'description' => "Payment account for {$this->model->name}",
                ];

                $contactData = [
                    'name' => $this->model->name,
                    'email' => $contactEmail,
                    'phone' => $contactPhone,
                ];

                $subaccountService->createSubAccount(
                    $this->model,
                    $bankData,
                    $contactData,
                    $percentageCharge,
                    ! $this->model->hasPrimarySubaccount(), // Set as primary only if no primary exists
                    $this->accountName
                );
                session()->flash('success', 'Account information added successfully!');
            }

            $this->showAccountModal = false;
            $this->resetAccountForm();
            $this->loadSubaccounts();
            $this->dispatch('accountUpdated');
        } catch (Exception $e) {
            $errorMessage = 'Failed to save account: '.$e->getMessage();
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
        $subaccount = $this->model->primarySubaccount();

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
        $subaccount = Subaccount::find($this->editingAccountId);

        if ($subaccount && $subaccount->subaccountable_id === $this->model->id) {
            $this->accountBank = $subaccount->settlement_bank ?? '';
            $this->accountBankCode = $subaccount->bank_code ?? '';
            $this->accountNumber = $subaccount->account_number ?? '';
            $this->accountName = $subaccount->business_name ?? $this->model->name;
            $this->showAccountModal = true;
        }
    }

    /**
     * Open edit modal for a specific subaccount
     */
    public function openEditModal($subaccountId): void
    {
        $this->editingAccountId = $subaccountId;
        $this->editAccount();
    }

    /**
     * Delete a specific subaccount
     */
    public function deleteAccount($subaccountId = null): void
    {
        try {
            $accountId = $subaccountId ?? $this->editingAccountId;

            $subaccount = Subaccount::find($accountId);

            if (! $subaccount || $subaccount->subaccountable_id !== $this->model->id) {
                session()->flash('error', 'Subaccount not found.');

                return;
            }

            $subaccountService = app(SubaccountPaymentService::class);
            $subaccountService->deleteSubAccount($subaccount, false); // Soft delete

            session()->flash('success', 'Account information removed successfully!');
            $this->loadSubaccounts();
            $this->dispatch('accountDeleted');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to delete account: '.$e->getMessage());
        }
    }

    /**
     * Set a subaccount as primary
     */
    public function setPrimarySubaccount($subaccountId): void
    {
        try {
            $subaccount = Subaccount::find($subaccountId);

            if (! $subaccount || $subaccount->subaccountable_id !== $this->model->id) {
                session()->flash('error', 'Subaccount not found.');

                return;
            }

            $this->model->setPrimarySubaccount($subaccount);
            session()->flash('success', 'Primary account updated successfully!');
            $this->loadSubaccounts();
            $this->dispatch('accountUpdated');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to update primary account: '.$e->getMessage());
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
