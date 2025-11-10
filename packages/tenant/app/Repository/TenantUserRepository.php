<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TenantUser;
use MonkeysLegion\Repository\EntityRepository;
use MonkeysLegion\Entity\Attributes\Uuid;

/**
 * @extends EntityRepository<TenantUser>
 */
class TenantUserRepository extends EntityRepository
{
    /** @var non-empty-string */
    protected string $table       = 'tenantuser';
    protected string $entityClass = TenantUser::class;

    // ──────────────────────────────────────────────────────────
    //  Typed convenience wrappers (optional)
    //  Keep them if you like the stricter return types; otherwise
    //  feel free to delete them and rely on the parent methods.
    // ──────────────────────────────────────────────────────────

    /**
     * @param array<string,mixed> $criteria
     * @return TenantUser[]
     */
    public function findAll(
        array $criteria = [],
        bool  $loadRelations = true
    ): array {
        /** @var TenantUser[] $rows */
        $rows = parent::findAll($criteria, $loadRelations);
        return $rows;
    }

    /**
     * @param array<string,mixed> $criteria
     */
    public function findOneBy(
        array $criteria,
        bool  $loadRelations = true
    ): ?TenantUser {
        /** @var ?TenantUser $row */
        $row = parent::findOneBy($criteria, $loadRelations);
        return $row;
    }

    public function findById( // TODO: to test, usually we have to refine the EntityRepository to support Uuid in such methods
        Uuid $id,
        bool   $loadRelations = true
    ): ?TenantUser {
        /** @var ?TenantUser $row */
        $row = parent::find($id, $loadRelations);
        return $row;
    }
}
