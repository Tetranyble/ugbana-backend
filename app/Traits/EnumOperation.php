<?php

namespace App\Traits;

trait EnumOperation
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    /**
     * @param  string  $enum  Example: DocumentStatus::class
     */
    public static function exists(string $name): bool
    {
        return array_search(strtoupper($name), self::values());
    }

    public static function fromName(string $name)
    {
        foreach (self::cases() as $platform) {
            if ($name === $platform->name) {
                return $platform;
            }
        }

        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }

    public static function fromValue(string $name)
    {
        foreach (self::cases() as $case) {
            if ($name === $case->value) {
                return $case;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }

    public static function insensitiveName(string $name)
    {
        return self::fromName(strtoupper($name));
    }

    public static function get(?string $name)
    {
        if ($name !== null && self::exists($name)) {
            return self::insensitiveName($name);
        }

        return $name;
    }
}
