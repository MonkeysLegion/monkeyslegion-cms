<?php

declare(strict_types=1);

namespace MonkeysLegion\TenantCore\Core;

use App\Entity\Tenant;

class TenantContext
{
    private static ?Tenant $current = null;

    public static function set(Tenant $tenant): void
    {
        self::$current = $tenant;
    }

    public static function get(): ?Tenant
    {
        return self::$current;
    }

    public static function clear(): void
    {
        self::$current = null;
    }

    public static function id(): ?string
    {
        return self::$current?->getId();
    }
}
