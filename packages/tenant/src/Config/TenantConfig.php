<?php

declare(strict_types=1);

namespace MonkeysLegion\TenantCore\Config;

class TenantConfig
{
    /**
     * Shared config array
     */
    private static ?array $config = null;

    /**
     * Get the config (load only once)
     */
    public static function getConfig(): array
    {
        if (self::$config === null) {
            $tenantConfig = include __DIR__ . '/../../config/tenant.php';
            self::$config = $tenantConfig['tenant'] ?? [];
        }

        return self::$config;
    }

    private function __construct() {}
}
