<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TenantModuleConfig;
use MonkeysLegion\Repository\EntityRepository;

/**
 * @extends EntityRepository<TenantModuleConfig>
 */
class TenantModuleConfigRepository extends EntityRepository
{
    /** @var non-empty-string */
    protected string $table       = 'tenantmoduleconfig';
    protected string $entityClass = TenantModuleConfig::class;

    // ──────────────────────────────────────────────────────────
    //  Typed convenience wrappers (optional)
    //  Keep them if you like the stricter return types; otherwise
    //  feel free to delete them and rely on the parent methods.
    // ──────────────────────────────────────────────────────────

    /**
     * @param array<string,mixed> $criteria
     * @return TenantModuleConfig[]
     */
    public function findAll(
        array $criteria = [],
        bool  $loadRelations = true
    ): array {
        /** @var TenantModuleConfig[] $rows */
        $rows = parent::findAll($criteria, $loadRelations);
        return $rows;
    }

    /**
     * @param array<string,mixed> $criteria
     */
    public function findOneBy(
        array $criteria,
        bool  $loadRelations = true
    ): ?TenantModuleConfig {
        /** @var ?TenantModuleConfig $row */
        $row = parent::findOneBy($criteria, $loadRelations);
        return $row;
    }
}
