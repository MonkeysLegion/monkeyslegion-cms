<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tenant;
use App\Service\TenantService;
use App\Repository\TenantRepository;
use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\ServerRequest;

/**
 * TenantController handles CRUD operations for Tenant entities.
 */
final class TenantController
{
    public function __construct(
        private TenantService $tenantService,
        private TenantRepository $tenantRepository
    ) {}

    /**
     * Create a new tenant.
     */
    #[Route(methods: 'POST', path: '/api/tenants', name: 'tenant.create', tags: ['Tenant'])]
    public function create(ServerRequest $request): Response
    {
        try {
            $body = json_decode((string) $request->getBody(), true);

            if (!isset($body['name'], $body['domain'])) {
                return json([
                    "status" => "error",
                    "message" => "Missing required fields: name, domain"
                ], 400)->withHeader('Content-Type', 'application/json');
            }

            $tenant = new Tenant();
            $tenant->setName($body['name']);
            $tenant->setDomain($body['domain']);
            $tenant->setStatus($body['status'] ?? 'active');
            $tenant->setSettings($body['settings'] ?? []);
            $tenant->setCreatedAt(new \DateTimeImmutable());
            $tenant->setUpdatedAt(new \DateTimeImmutable());

            $this->tenantService->createTenant($tenant);

            // Assign modules if provided
            if (isset($body['modules']) && is_array($body['modules'])) {
                $this->tenantService->assignModules($tenant, $body['modules']);
            }

            return json([
                "status" => "success",
                "message" => "Tenant created successfully",
                "data" => $this->formatTenant($tenant)
            ], 201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get tenant by ID with optional field selection.
     */
    #[Route(methods: 'GET', path: '/api/tenants/{id}', name: 'tenant.show', tags: ['Tenant'])]
    public function show(string $id, ServerRequest $request): Response
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            // Get fields parameter from query string
            $queryParams = $request->getQueryParams();
            $fields = isset($queryParams['fields']) ? explode(',', $queryParams['fields']) : null;

            return json([
                "status" => "success",
                "data" => $this->formatTenant($tenant, $fields)
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 400)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * List all tenants with optional field selection and filtering.
     */
    #[Route(methods: 'GET', path: '/api/tenants', name: 'tenant.index', tags: ['Tenant'])]
    public function index(ServerRequest $request): Response
    {
        try {
            $queryParams = $request->getQueryParams();

            // Build criteria from query params
            $criteria = [];
            if (isset($queryParams['status'])) {
                $criteria['status'] = $queryParams['status'];
            }
            if (isset($queryParams['domain'])) {
                $criteria['domain'] = $queryParams['domain'];
            }

            $tenants = $this->tenantRepository->findAll($criteria);

            // Get fields parameter
            $fields = isset($queryParams['fields']) ? explode(',', $queryParams['fields']) : null;

            $data = array_map(fn($tenant) => $this->formatTenant($tenant, $fields), $tenants);

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
     * Update tenant - supports partial updates.
     */
    #[Route(methods: 'PATCH', path: '/api/tenants/{id}', name: 'tenant.update', tags: ['Tenant'])]
    public function update(string $id, ServerRequest $request): Response
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $body = json_decode((string) $request->getBody(), true);

            // Update only provided fields
            if (isset($body['name'])) {
                $tenant->setName($body['name']);
            }
            if (isset($body['domain'])) {
                $tenant->setDomain($body['domain']);
            }
            if (isset($body['status'])) {
                $tenant->setStatus($body['status']);
            }
            if (isset($body['settings'])) {
                // Support partial settings update
                if (isset($body['merge_settings']) && $body['merge_settings'] === true) {
                    $currentSettings = $tenant->getSettings();
                    $newSettings = array_merge($currentSettings, $body['settings']);
                    $tenant->setSettings($newSettings);
                } else {
                    $tenant->setSettings($body['settings']);
                }
            }

            $tenant->setUpdatedAt(new \DateTimeImmutable());

            $updateResult = $this->tenantRepository->save($tenant);

            if ($updateResult === 0) {
                return json([
                    "status" => "warning",
                    "message" => "Update may have failed",
                    "data" => $this->formatTenant($tenant)
                ], 500)->withHeader('Content-Type', 'application/json');
            }

            return json([
                "status" => "success",
                "message" => "Tenant updated successfully",
                "data" => $this->formatTenant($tenant)
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Update only tenant settings (config).
     */
    #[Route(methods: 'PATCH', path: '/api/tenants/{id}/settings', name: 'tenant.update.settings', tags: ['Tenant'])]
    public function updateSettings(string $id, ServerRequest $request): Response
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $body = json_decode((string) $request->getBody(), true);

            if (!isset($body['settings'])) {
                return json([
                    "status" => "error",
                    "message" => "Missing settings field"
                ], 400)->withHeader('Content-Type', 'application/json');
            }

            // Merge with existing settings by default
            $currentSettings = $tenant->getSettings();
            $mergedSettings = array_merge($currentSettings, $body['settings']);
            $tenant->setSettings($mergedSettings);
            $tenant->setUpdatedAt(new \DateTimeImmutable());

            $this->tenantRepository->save($tenant);

            return json([
                "status" => "success",
                "message" => "Settings updated successfully",
                "data" => [
                    "id" => $tenant->getId(),
                    "settings" => $tenant->getSettings()
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
     * Delete tenant.
     */
    #[Route(methods: 'DELETE', path: '/api/tenants/{id}', name: 'tenant.delete', tags: ['Tenant'])]
    public function delete(string $id): Response
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $this->tenantRepository->delete($tenant);

            return json([
                "status" => "success",
                "message" => "Tenant deleted successfully"
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Get tenant modules.
     */
    #[Route(methods: 'GET', path: '/api/tenants/{id}/modules', name: 'tenant.modules', tags: ['Tenant'])]
    public function getModules(string $id): Response
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $modules = $this->tenantService->getTenantModules($tenant);

            return json([
                "status" => "success",
                "data" => $modules
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Assign modules to tenant.
     */
    #[Route(methods: 'POST', path: '/api/tenants/{id}/modules', name: 'tenant.assign.modules', tags: ['Tenant'])]
    public function assignModules(string $id, ServerRequest $request): Response
    {
        try {
            $tenant = $this->tenantRepository->findById($id);

            if (!$tenant) {
                return json([
                    "status" => "error",
                    "message" => "Tenant not found"
                ], 404)->withHeader('Content-Type', 'application/json');
            }

            $body = json_decode((string) $request->getBody(), true);

            if (!isset($body['modules']) || !is_array($body['modules'])) {
                return json([
                    "status" => "error",
                    "message" => "Missing modules array"
                ], 400)->withHeader('Content-Type', 'application/json');
            }

            $this->tenantService->assignModules($tenant, $body['modules']);

            return json([
                "status" => "success",
                "message" => "Modules assigned successfully"
            ], 200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Format tenant data with optional field selection.
     */
    private function formatTenant(Tenant $tenant, ?array $fields = null): array
    {
        $data = [
            "id" => $tenant->getId(),
            "name" => $tenant->getName(),
            "domain" => $tenant->getDomain(),
            "status" => $tenant->getStatus(),
            "settings" => $tenant->getSettings(),
            "created_at" => $tenant->getCreatedAt()->format('Y-m-d H:i:s'),
            "updated_at" => $tenant->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        // Filter fields if specified
        if ($fields !== null) {
            $data = array_intersect_key($data, array_flip($fields));
        }

        return $data;
    }
}
