<?php

namespace App\Support;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Mark implements Castable
{
    public function __construct(private ?string $up, private ?string $down)
    {

    }

    public static function fromArray(array $array)
    {
        return new static($array['up'], $array['down']);
    }

    public static function fromString(string $string)
    {
        return static::fromArray(json_decode($string, true));
    }

    public function toArray(): array
    {
        return [
            'up' => $this->up,
            'down' => $this->down,
        ];
    }

    public function toString(): string
    {
        return json_encode($this->toArray());
    }

    public function up(): ?string
    {
        return $this->up;
    }

    public function down(): ?string
    {
        return $this->down;
    }

    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes {
            public function get($model, string $key, $value, array $attributes)
            {
                return is_null($value) ? $value : Mark::fromString($value);
            }

            public function set($model, string $key, $value, array $attributes)
            {
                if (is_string($value)) {
                    $value = Mark::fromString($value);
                }

                if (is_array($value)) {
                    $value = Mark::fromArray($value);
                }

                if (!$value instanceof Mark) {
                    throw new \InvalidArgumentException('Expected an instanceof of App\Support\Mark');
                }

                return $value->toString();
            }
        };
    }
}
