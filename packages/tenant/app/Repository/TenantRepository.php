<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tenant;
use MonkeysLegion\Repository\EntityRepository;

/**
 * @extends EntityRepository<Tenant>
 */
class TenantRepository extends EntityRepository
{
    /** @var non-empty-string */
    protected string $table       = 'tenant';
    protected string $entityClass = Tenant::class;

    // ──────────────────────────────────────────────────────────
    //  Typed convenience wrappers (optional)
    //  Keep them if you like the stricter return types; otherwise
    //  feel free to delete them and rely on the parent methods.
    // ──────────────────────────────────────────────────────────

    /**
     * @param array<string,mixed> $criteria
     * @return Tenant[]
     */
    public function findAll(
        array $criteria = [],
        bool  $loadRelations = true
    ): array {
        /** @var Tenant[] $rows */
        $rows = parent::findAll($criteria, $loadRelations);
        return $rows;
    }

    /**
     * @param array<string,mixed> $criteria
     */
    public function findOneBy(
        array $criteria,
        bool  $loadRelations = true
    ): ?Tenant {
        /** @var ?Tenant $row */
        $row = parent::findOneBy($criteria, $loadRelations);
        return $row;
    }

    public function findById(
        string $id,
        bool   $loadRelations = true
    ): ?Tenant {
        /** @var ?Tenant $row */
        $row = parent::findOneBy(['id' => $id], $loadRelations);
        return $row;
    }

    public function findByDomain(
        string $domain,
        bool   $loadRelations = true
    ): ?Tenant {
        /** @var ?Tenant $row */
        $row = parent::findOneBy(['domain' => $domain], $loadRelations);
        return $row;
    }
}
