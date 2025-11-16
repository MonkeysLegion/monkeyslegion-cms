<?php

declare(strict_types=1);

namespace MonkeysLegion\TenantCore\Services;

use App\Entity\Tenant;
use App\Repository\TenantRepository;
use App\Repository\TenantModuleConfigRepository;
use MonkeysLegion\TenantCore\Contracts\TenantServiceInterface;
use MonkeysLegion\TenantCore\Core\TenantContext;

class TenantService implements TenantServiceInterface
{
    public function __construct(
        private TenantRepository $tenantRepository,
        private TenantModuleConfigRepository $moduleConfigRepository
    ) {}

    public function createTenant(Tenant $tenant): void
    {
        $this->tenantRepository->save($tenant);
    }

    public function assignModules(Tenant $tenant, array $modules): void
    {
        foreach ($modules as $module) {
            // Create or update module config for this tenant
        }
    }

    public function getTenantModules(Tenant $tenant): array
    {
        // Find all module configs for this tenant
        return $this->moduleConfigRepository->findAll([
            'tenant_id' => $tenant->getId()
        ]);
    }

    public function loadTenantContext(Tenant $tenant): void
    {
        TenantContext::set($tenant);
    }

    public function syncWithEngine(Tenant $tenant): void
    {
        // Sync tenant data with the engine package
        // Implementation depends on the inter-package communication strategy
    }
}
