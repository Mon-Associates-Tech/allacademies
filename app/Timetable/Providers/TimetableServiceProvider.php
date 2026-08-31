<?php
// app/Timetable/Providers/TimetableServiceProvider.php

namespace App\Timetable\Providers;

use App\Timetable\Livewire\RoomManager;
use App\Timetable\Livewire\TimeSlotManager;
use App\Timetable\Livewire\TimetableGrid;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Registers the Timetable module's migrations and routes from their own
 * namespaced subfolder, mirroring BookShop's approach — keeps the module's
 * schema and endpoints self-contained rather than mixed into the host app's
 * root migrations/routes files.
 *
 * Unlike BookShopServiceProvider, this module has no separate auth guards:
 * Timetable is integration-only and authenticates through the host app's
 * existing 'web' guard, so there's nothing to register in register().
 */
class TimetableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Kept in their own subfolder (database/migrations/timetable) rather
        // than the app's root migrations folder, so the module's schema
        // stays clearly scoped and easy to locate/extract later if needed.
        $this->loadMigrationsFrom(database_path('migrations/timetable'));

        $this->loadRoutesFrom(base_path('routes/timetable.php'));


        // TimetableGrid lives under App\Timetable\Livewire rather than the
        // default App\Livewire namespace Livewire auto-discovers, so it
        // needs explicit registration — otherwise <livewire:timetable.grid>
        // resolves to nothing at render time with no clear error.
        Livewire::component('timetable.grid', TimetableGrid::class);
        Livewire::component('timetable.rooms', RoomManager::class);
        Livewire::component('timetable.time-slots', TimeSlotManager::class);

        \Illuminate\Support\Facades\Blade::anonymousComponentPath(
            resource_path('views/timetable/components'),
            'timetable'
        );
    }
}
