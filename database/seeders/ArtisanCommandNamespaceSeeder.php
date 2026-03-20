<?php

namespace Database\Seeders;

use App\Models\ArtisanCommandNamespace;
use Illuminate\Database\Seeder;

class ArtisanCommandNamespaceSeeder extends Seeder
{
    public function run(): void
    {
        // Automatically detect and sync all namespaces
        ArtisanCommandNamespace::syncNamespaces();
    }
}
