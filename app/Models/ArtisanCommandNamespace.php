<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class ArtisanCommandNamespace extends Model
{
    protected $fillable = [
        'namespace',
        'label',
        'description',
        'is_enabled',
        'is_laravel_core',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_laravel_core' => 'boolean',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeUserDefined($query)
    {
        return $query->where('is_laravel_core', false);
    }

    public function scopeLaravelCore($query)
    {
        return $query->where('is_laravel_core', true);
    }

    public static function getEnabledNamespaces(): array
    {
        return static::enabled()
            ->orderBy('sort_order')
            ->pluck('namespace')
            ->toArray();
    }

    public static function syncNamespaces(): void
    {
        $laravelCoreNamespaces = [
            'about', 'cache', 'config', 'db', 'env', 'event', 'key', 'make',
            'migrate', 'model', 'optimize', 'package', 'queue', 'route',
            'sail', 'sanctum', 'schedule', 'schema', 'storage', 'stub',
            'telescope', 'vendor', 'view', 'livewire', 'tinker'
        ];

        $destructiveNamespaces = ['db', 'migrate', 'schema'];

        $namespaceDescriptions = [
            'db' => 'Database commands (DESTRUCTIVE - can wipe data)',
            'migrate' => 'Database migration commands (can alter schema)',
            'schema' => 'Database schema commands (can alter structure)',
            'cache' => 'Cache management commands',
            'config' => 'Configuration cache commands',
            'route' => 'Route management commands',
            'view' => 'View cache commands',
            'queue' => 'Queue management commands',
            'optimize' => 'Application optimization commands',
            'storage' => 'Storage management commands',
            'schedule' => 'Task scheduling commands',
            'make' => 'Code generation commands',
            'key' => 'Application key generation',
            'users' => 'User management commands',
            'books' => 'Book management commands',
            'students' => 'Student management commands',
            'tokens' => 'Token management commands',
            'subscriptions' => 'Subscription management commands',
            'openai' => 'OpenAI integration commands',
            'messages' => 'Message management commands',
        ];

        $allCommands = collect(Artisan::all());
        $detectedNamespaces = $allCommands
            ->map(fn($command) => explode(':', $command->getName())[0])
            ->unique()
            ->filter(fn($namespace) => $namespace !== 'completion')
            ->values();

        foreach ($detectedNamespaces as $namespace) {
            $isLaravelCore = in_array($namespace, $laravelCoreNamespaces);
            $isDestructive = in_array($namespace, $destructiveNamespaces);
            $description = $namespaceDescriptions[$namespace] ?? 
                          ($isLaravelCore ? 'Laravel core commands' : 'Application commands');
            
            static::firstOrCreate(
                ['namespace' => $namespace],
                [
                    'label' => ucfirst(str_replace('-', ' ', $namespace)),
                    'description' => $description,
                    'is_enabled' => !$isLaravelCore, // Enable user-defined by default
                    'is_laravel_core' => $isLaravelCore,
                    'sort_order' => $isLaravelCore ? 100 : 10,
                ]
            );
        }
    }
}
