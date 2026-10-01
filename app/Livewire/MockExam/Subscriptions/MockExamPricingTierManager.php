<?php

namespace App\Livewire\MockExam\Subscriptions;

use App\MockExam\Models\MockExamPricingTier;
use App\MockExam\Models\MockExamUserGroup;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MockExamPricingTierManager extends Component
{
    public array $tiers = [];

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $userGroup = 'individual';

    public int $subjectCount = 1;

    public string $pricePerStudent = '';

    public string $printFlatRate = '';

    public bool $isActive = true;

    public function mount(): void
    {
        $this->loadTiers();
    }

    public function loadTiers(): void
    {
        $this->tiers = MockExamPricingTier::query()
            ->orderBy('subject_count')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getUserGroupOptionsProperty(): array
    {
        return MockExamUserGroup::options();
    }

    public function openCreate(): void
    {
        $this->reset([
            'editingId', 'name', 'userGroup', 'subjectCount',
            'pricePerStudent', 'printFlatRate', 'isActive',
        ]);
        $this->isActive = true;
        $this->userGroup = 'individual';
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $tier = MockExamPricingTier::findOrFail($id);

        $this->editingId = $id;
        $this->name = (string) ($tier->name ?? '');
        $this->userGroup = $tier->user_group instanceof MockExamUserGroup
            ? $tier->user_group->value
            : (string) ($tier->user_group ?? 'individual');
        $this->subjectCount = (int) $tier->subject_count;
        $this->pricePerStudent = (string) $tier->price_per_student;
        $this->printFlatRate = (string) $tier->print_flat_rate;
        $this->isActive = (bool) $tier->is_active;

        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('mock_exam_pricing_tiers', 'name')->ignore($this->editingId),
            ],
            'userGroup'       => 'required|in:individual,school,local_government,organization',
            'subjectCount'    => 'required|integer|min:1|max:20',
            'pricePerStudent' => 'required|numeric|min:0',
            'printFlatRate'   => 'required|numeric|min:0',
        ]);

        $data = [
            'name'              => $this->name,
            'user_group'        => $this->userGroup,
            'subject_count'     => $this->subjectCount,
            'price_per_student' => $this->pricePerStudent,
            'print_flat_rate'   => $this->printFlatRate,
            'is_active'         => $this->isActive,
        ];

        if ($this->editingId) {
            MockExamPricingTier::findOrFail($this->editingId)->update($data);

            $this->dispatch('flash', type: 'success', message: 'Pricing tier updated.');
        } else {
            MockExamPricingTier::create($data);

            $this->dispatch('flash', type: 'success', message: 'Pricing tier created.');
        }

        $this->showForm = false;

        $this->loadTiers();
    }

    public function toggleActive(int $id): void
    {
        $tier = MockExamPricingTier::findOrFail($id);

        $tier->update([
            'is_active' => ! $tier->is_active,
        ]);

        $this->loadTiers();
    }

    public function delete(int $id): void
    {
        MockExamPricingTier::findOrFail($id)->delete();

        $this->loadTiers();

        $this->dispatch('flash', type: 'success', message: 'Tier deleted.');
    }

    public function render()
    {
        return view('livewire.mock-exam.subscriptions.mock-exam-pricing-tier-manager');
    }
}
