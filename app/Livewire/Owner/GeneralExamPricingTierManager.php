<?php

namespace App\Livewire\Owner;

use App\Models\GeneralExamPricingTier;
use Livewire\Component;

class GeneralExamPricingTierManager extends Component
{
    public array $tiers = [];

    public bool $showForm = false;

    public ?int $editingId = null;

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
        $this->tiers = GeneralExamPricingTier::orderBy('subject_count')->get()->toArray();
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'subjectCount', 'pricePerStudent', 'printFlatRate', 'isActive']);
        $this->isActive = true;
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $tier = GeneralExamPricingTier::findOrFail($id);
        $this->editingId = $id;
        $this->subjectCount = $tier->subject_count;
        $this->pricePerStudent = (string) $tier->price_per_student;
        $this->printFlatRate = (string) $tier->print_flat_rate;
        $this->isActive = $tier->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'subjectCount' => 'required|integer|min:1|max:20',
            'pricePerStudent' => 'required|numeric|min:0',
            'printFlatRate' => 'required|numeric|min:0',
        ]);

        $data = [
            'subject_count' => $this->subjectCount,
            'price_per_student' => $this->pricePerStudent,
            'print_flat_rate' => $this->printFlatRate,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            GeneralExamPricingTier::findOrFail($this->editingId)->update($data);
            $this->dispatch('flash', type: 'success', message: 'Pricing tier updated.');
        } else {
            GeneralExamPricingTier::create($data);
            $this->dispatch('flash', type: 'success', message: 'Pricing tier created.');
        }

        $this->showForm = false;
        $this->loadTiers();
    }

    public function toggleActive(int $id): void
    {
        $tier = GeneralExamPricingTier::findOrFail($id);
        $tier->update(['is_active' => ! $tier->is_active]);
        $this->loadTiers();
    }

    public function delete(int $id): void
    {
        GeneralExamPricingTier::findOrFail($id)->delete();
        $this->loadTiers();
        $this->dispatch('flash', type: 'success', message: 'Tier deleted.');
    }

    public function render()
    {
        return view('livewire.owner.general-exam-pricing-tier-manager');
    }
}
