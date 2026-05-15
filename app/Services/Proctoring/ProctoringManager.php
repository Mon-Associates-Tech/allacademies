<?php
/**
 * Proctoring Manager
 *
 * Resolves and manages pluggable proctoring drivers.
 * Acts as a facade/service container resolver to ensure
 * consistent driver usage across middleware and controllers.
 */
namespace App\Services\Proctoring;

use App\Contracts\ProctoringDriverInterface;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class ProctoringManager
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function driver(string $name = null): ProctoringDriverInterface
    {
        $name = $name ?: config('proctoring.default_driver');

        if (!isset(config('proctoring.drivers')[$name])) {
            throw new InvalidArgumentException("Proctoring driver [{$name}] is not defined.");
        }

        $class = config('proctoring.drivers')[$name];
        return $this->app->make($class);
    }
}
