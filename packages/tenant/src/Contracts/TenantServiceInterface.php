<?php

declare(strict_types=1);

namespace MonkeysLegion\TenantCore\Contracts;

use App\Entity\Tenant;

interface TenantServiceInterface
{
    public function createTenant(Tenant $tenant): void;
    public function assignModules(Tenant $tenant, array $modules): void;
    public function getTenantModules(Tenant $tenant): array;
    public function loadTenantContext(Tenant $tenant): void;
    public function syncWithEngine(Tenant $tenant): void;
}
