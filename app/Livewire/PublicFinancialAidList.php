<?php

namespace App\Livewire;

use App\Models\FinancialAid;
use App\Models\SchoolPayment;
use Livewire\Component;
use Livewire\WithPagination;

class PublicFinancialAidList extends Component
{
    use WithPagination;

    public function render()
    {
        // Fetch active financial aids with their school and beneficiaries
        $aids = FinancialAid::with(['school', 'beneficiaries.user'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        // Calculate stats for each aid
        $aids->getCollection()->transform(function ($aid) {
            // Assuming 'amount' field in FinancialAid is the Total Goal as per requirements
            $goal = $aid->amount;

            // Calculate realized amount from payments marked as 'donation' for this aid
            // If you don't have 'donation' types yet, this will return 0
            $realized = SchoolPayment::where('payment_type', 'donation')
                ->where('status', 'succeeded')
                ->whereJsonContains('metadata->financial_aid_id', $aid->id)
                ->sum('amount');

            $aid->goal_amount = $goal;
            $aid->realized_amount = $realized;
            $aid->left_amount = max(0, $goal - $realized);
            $aid->progress_percentage = $goal > 0 ? min(100, round(($realized / $goal) * 100)) : 0;

            return $aid;
        });

        return view('livewire.public-financial-aid-list', [
            'aids' => $aids
        ])->layout('components.layouts.guest', ['pageName' => 'Philanthropy & Aid']);
    }
}
