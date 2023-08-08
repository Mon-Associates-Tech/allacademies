<?php

namespace App\Traits;

use App\Models\Tracking;
use Illuminate\Database\Eloquent\Model;

/** @mixin Model */
trait Trackable
{
    protected static function bootTrackable(): void
    {
        static::created(fn (Model $model) => static::track('created', $model));
        static::updated(fn (Model $model) => static::track('updated', $model));
        static::deleted(fn (Model $model) => static::track('deleted', $model));
    }

    protected static function track(string $event, Model $model): void
    {
        $snapshot = 'updated' === $event
            ? ['value' => $model->toArray(), 'changes' => $model->getRawOriginal()]
            : ['value' => $model->toArray()];

        $tracking = new Tracking(['event' => $event, 'snapshot' => $snapshot]);
        $tracking->trackable()->associate($model);
        $tracking->causer_id = auth()->id();
        $tracking->save();

        $model->tracking = [];
    }
}
