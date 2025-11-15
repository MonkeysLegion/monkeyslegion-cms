<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TenantUser;
use App\Repository\TenantUserRepository;
use App\Repository\TenantRepository;
use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\ServerRequest;

/**
 * TenantUserController handles CRUD operations for TenantUser entities.
 */
final class TenantUserController
{
    public function __construct(
        private TenantUserRepository $tenantUserRepository,
        private TenantRepository $tenantRepository
    ) {}

    /**
     * Create a new tenant user.
     */
    #[Route(methods: 'POST', path: '/api/tenant-users', name: 'tenant_user.create', tags: ['TenantUser'])]
    public function create(ServerRequest $request): Response
    {
        try {
            $body = json_decode((string) $request->getBody(), true);

            if (!isset($body['tenant_id'], $body['user_id'], $body['role'])) {
                return json([
                    "status" => "error",
                    "message" => "Missing required fields: tenant_id, user_id, role"
                ], 400)->withHeader('Content-Type', 'application/json');
            }

            // Verify tenant exists
            $tenant = $this->tenantRepository->findById($body['tenant_id']);
            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $tenantUser = new TenantUser();
            $tenantUser->setUserId($body['user_id']);
            $tenantUser->setRole($body['role']);
            $tenantUser->setPermissions($body['permissions'] ?? []);
            $tenantUser->setTenant($tenant);

            $insertedId = $this->tenantUserRepository->save($tenantUser);

            if ($insertedId === 0) {
                return json([
                    "status" => "error",
                    "message" => "Failed to create tenant user"
                ], 500)->withHeader('Content-Type', 'application/json');
            }

            return json([
                "status" => "success",
                "message" => "Tenant user created successfully",
                "data" => $this->formatTenantUser($tenantUser)
            ], 201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get tenant user by ID with optional field selection.
     */
    #[Route(methods: 'GET', path: '/api/tenant-users/{id}', name: 'tenant_user.show', tags: ['TenantUser'])]
    public function show(string $id, ServerRequest $request): Response
    {
        try {
            $tenantUser = $this->tenantUserRepository->findOneBy(['id' => $id]);

            if (!$tenantUser) {
                return json([
                    "status" => "error",
                    "message" => "Tenant user not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $queryParams = $request->getQueryParams();
            $fields = isset($queryParams['fields']) ? explode(',', $queryParams['fields']) : null;

            return json([
                "status" => "success",
                "data" => $this->formatTenantUser($tenantUser, $fields)
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 400)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * List all tenant users with filtering.
     */
    #[Route(methods: 'GET', path: '/api/tenant-users', name: 'tenant_user.index', tags: ['TenantUser'])]
    public function index(ServerRequest $request): Response
    {
        try {
            $queryParams = $request->getQueryParams();

            $criteria = [];
            if (isset($queryParams['tenant_id'])) {
                $criteria['tenant_id'] = $queryParams['tenant_id'];
            }
            if (isset($queryParams['user_id'])) {
                $criteria['user_id'] = $queryParams['user_id'];
            }
            if (isset($queryParams['role'])) {
                $criteria['role'] = $queryParams['role'];
            }

            $tenantUsers = $this->tenantUserRepository->findAll($criteria);

            $fields = isset($queryParams['fields']) ? explode(',', $queryParams['fields']) : null;

            $data = array_map(fn($tu) => $this->formatTenantUser($tu, $fields), $tenantUsers);

            return json([
                "status" => "success",
                "count" => count($data),
                "data" => $data
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Update tenant user - supports partial updates.
     */
    #[Route(methods: 'PATCH', path: '/api/tenant-users/{id}', name: 'tenant_user.update', tags: ['TenantUser'])]
    public function update(string $id, ServerRequest $request): Response
    {
        try {
            $tenantUser = $this->tenantUserRepository->findOneBy(['id' => $id]);

            if (!$tenantUser) {
                return json([
                    "status" => "error",
                    "message" => "Tenant user not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $body = json_decode((string) $request->getBody(), true);

            if (isset($body['role'])) {
                $tenantUser->setRole($body['role']);
            }
            if (isset($body['permissions'])) {
                // Support partial permissions update
                if (isset($body['merge_permissions']) && $body['merge_permissions'] === true) {
                    $currentPermissions = $tenantUser->getPermissions();
                    $newPermissions = array_merge($currentPermissions, $body['permissions']);
                    $tenantUser->setPermissions($newPermissions);
                } else {
                    $tenantUser->setPermissions($body['permissions']);
                }
            }

            $updateResult = $this->tenantUserRepository->save($tenantUser);

            if ($updateResult === 0) {
                return json([
                    "status" => "warning",
                    "message" => "Update may have failed",
                    "data" => $this->formatTenantUser($tenantUser)
                ], 500)->withHeader('Content-Type', 'application/json');
            }

            return json([
                "status" => "success",
                "message" => "Tenant user updated successfully",
                "data" => $this->formatTenantUser($tenantUser)
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Update only permissions.
     */
    #[Route(methods: 'PATCH', path: '/api/tenant-users/{id}/permissions', name: 'tenant_user.update.permissions', tags: ['TenantUser'])]
    public function updatePermissions(string $id, ServerRequest $request): Response
    {
        try {
            $tenantUser = $this->tenantUserRepository->findOneBy(['id' => $id]);

            if (!$tenantUser) {
                return json([
                    "status" => "error",
                    "message" => "Tenant user not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $body = json_decode((string) $request->getBody(), true);

            if (!isset($body['permissions'])) {
                return json([
                    "status" => "error",
                    "message" => "Missing permissions field"
                ], 400)->withHeader('Content-Type', 'application/json');
            }

            $currentPermissions = $tenantUser->getPermissions();
            $mergedPermissions = array_merge($currentPermissions, $body['permissions']);
            $tenantUser->setPermissions($mergedPermissions);

            $this->tenantUserRepository->save($tenantUser);

            return json([
                "status" => "success",
                "message" => "Permissions updated successfully",
                "data" => [
                    "id" => $tenantUser->getId(),
                    "permissions" => $tenantUser->getPermissions()
                ]
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Delete tenant user.
     */
    #[Route(methods: 'DELETE', path: '/api/tenant-users/{id}', name: 'tenant_user.delete', tags: ['TenantUser'])]
    public function delete(string $id): Response
    {
        try {
            $tenantUser = $this->tenantUserRepository->findOneBy(['id' => $id]);

            if (!$tenantUser) {
                return json([
                    "status" => "error",
                    "message" => "Tenant user not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $this->tenantUserRepository->delete($tenantUser);

            return json([
                "status" => "success",
                "message" => "Tenant user deleted successfully"
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Format tenant user data with optional field selection.
     */
    private function formatTenantUser(TenantUser $tenantUser, ?array $fields = null): array
    {
        $data = [
            "id" => $tenantUser->getId(),
            "user_id" => $tenantUser->getUserId(),
            "role" => $tenantUser->getRole(),
            "permissions" => $tenantUser->getPermissions(),
            "tenant_id" => $tenantUser->getTenant()->getId(),
            "tenant_name" => $tenantUser->getTenant()->getName()
        ];

        if ($fields !== null) {
            $data = array_intersect_key($data, array_flip($fields));
        }

        return $data;
    }
}
