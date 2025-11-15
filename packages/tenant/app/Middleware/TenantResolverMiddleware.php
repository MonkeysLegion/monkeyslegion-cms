<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repository\TenantRepository;
use App\Service\TenantService;
use MonkeysLegion\Http\Message\JsonResponse;
use MonkeysLegion\TenantCore\Core\TenantContext;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

final class TenantResolverMiddleware implements MiddlewareInterface
{
    private array $excludedPaths = [
        '/healthz',
    ];

    public function __construct(
        private TenantRepository $tenantRepository,
        private TenantService $tenantService
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tenant = null;

        if (in_array($request->getUri()->getPath(), $this->excludedPaths, true)) {
            return $handler->handle($request);
        }

        // ───────────────────────────────────────────────
        // 1) Detect from domain first
        // ───────────────────────────────────────────────
        $host = $request->getUri()->getHost();

        if ($host !== '') {
            // Try full domain (most common)
            $tenant = $this->tenantRepository->findByDomain($host);

            // If not found, try subdomain only (tenant.example.com → tenant)
            if ($tenant === null) {
                $parts = explode('.', $host);
                if (count($parts) > 2) {
                    $sub = $parts[0];
                    $tenant = $this->tenantRepository->findByDomain($sub);
                }
            }
        }

        // ───────────────────────────────────────────────
        // 2) Fallback: X-Tenant header (tenant id)
        // ───────────────────────────────────────────────
        if ($tenant === null) {
            $tenantIdHeader = $request->getHeaderLine('X-Tenant');
            if ($tenantIdHeader !== '') {
                $tenant = $this->tenantRepository->findById($tenantIdHeader);
            }
        }

        // ───────────────────────────────────────────────
        // 3) Still not found? Reject
        // ───────────────────────────────────────────────
        if ($tenant === null) {
            return new JsonResponse([
                'error' => 'Tenant not found'
            ], 404);
        }

        // ───────────────────────────────────────────────
        // 4) Set tenant in context for current request
        // ───────────────────────────────────────────────
        $this->tenantService->loadTenantContext($tenant);

        return $handler->handle($request);
    }
}
