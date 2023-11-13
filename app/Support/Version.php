<?php

namespace App\Support;

final class Version
{
    public const MAJOR = 1;
    public const MINOR = 0;
    public const PATCH = 0;
    public const META = 'beta.11';

    public static function full()
    {
        $version = sprintf('v%d.%d.%d', self::MAJOR, self::MINOR, self::PATCH);

        if ('stable' !== self::META) {
            $version = sprintf('%s-%s', $version, self::META);
        }

        return $version;
    }
}
