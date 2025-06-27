<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\AcademicSubject;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionPackage;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    public function index()
    {
        $currentTeam = Team::query()->find(auth()->user()->current_team_id);
        if(!$currentTeam){
            $currentTeam = Team::query()->where('owner_id', auth()->id())->first();
        }

        $academicSubjects = AcademicSubject::query()->with('academicLevel.academicGroup')->whereHas('subscriptions', function (Builder $query) {
            $query->where('status', SubscriptionStatus::PAID)
                ->where('expires_at', '>', now())
                ->where('team_id', auth()->user()->current_team_id)
                ->where(function (Builder $query) {
                    $query->where(function (Builder $query) {
                        $query->where('package', SubscriptionPackage::INSTITUTION_FULL)->where(function (Builder $query) {
                            $query->whereRelation('team', 'owner_id', auth()->id())->orWhereHas('team', function (Builder $query) {
                                $query->whereRelation('members', 'user_id', auth()->id());
                            });
                        });
                    })->orWhere(function (Builder $query) {
                        $query->where('package', SubscriptionPackage::INDIVIDUAL_FULL)->whereRelation('subscriber', 'id', auth()->id());
                    });
                });
        })->latest('id')->paginate();

        return view('dashboard', [
            'academicSubjects' => $academicSubjects,
            'currentTeam' => $currentTeam,
        ]);
    }
}
