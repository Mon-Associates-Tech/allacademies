<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class SchoolScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Skip scoping for User model to avoid circular references
        if ($model instanceof \App\Models\User) {
            return;
        }

        // Check if we're in a web context with auth available
        if (!Auth::hasUser() || !Auth::check()) {
            return; // Skip scoping if no authenticated user
        }

        $user = Auth::user();
        // Skip scoping for super admins and owners
        if ($user && $user->hasAnyRole(['owner', 'super-admin', 'admin'])) {
            return;
        }

        // Apply school scoping
        if ($user && $user->school_id) {
            $builder->where($model->getTable() . '.school_id', $user->school_id);
        }
    }
}
