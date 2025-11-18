<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\Tenant;
use MonkeysLegion\Database\Cache\Adapters\RedisCacheAdapter;
use MonkeysLegion\Database\Cache\Contracts\CacheItemPoolInterface;
use MonkeysLegion\Http\Message\JsonResponse;
use MonkeysLegion\Query\QueryBuilder;
use MonkeysLegion\TenantCore\Config\TenantConfig;
use MonkeysLegion\TenantCore\Contracts\TenantServiceInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

final class TenantResolverMiddleware implements MiddlewareInterface
{
    private readonly string $tenantKey;
    private readonly int $tenantTtl;
    private array $excludedPaths = [
        '/healthz',
    ];

    public function __construct(
        private QueryBuilder $queryBuilder,
        private TenantServiceInterface $tenantService,
        private CacheItemPoolInterface $cachePool,
    ) {
        $config = TenantConfig::getConfig();
        $this->tenantKey = $config['key_prefix'];
        $this->tenantTtl = (int) $config['ttl'];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getUri()->getPath(), $this->excludedPaths, true)) {
            return $handler->handle($request);
        }

        ['uuid' => $tenantUuid, 'domain' => $domain] = $this->extractTenantInfo($request);

        if ($tenantUuid === null && $domain === null) {
            return $this->notFoundResponse('Tenant identification missing');
        }

        // Try UUID first if present
        $tenantItem = $this->cachePool->getItem($this->tenantKey . $tenantUuid);
        $tenantCacheCheck = $tenantUuid
            ? $tenantItem->get()
            : null;
        $tenant = $tenantCacheCheck instanceof Tenant ? $tenantCacheCheck : null;

        // Fallback to domain if UUID not cached or missing
        if ($tenant === null && $domain !== null) {
            $tenantItem = $this->cachePool->getItem($this->tenantKey . $domain);
            $tenantCacheCheck = $tenantItem->get();
            $tenant = $tenantCacheCheck instanceof Tenant ? $tenantCacheCheck : null;
        }

        if ($tenant !== null && $tenantItem->isHit()) {
            return $this->continue($tenant, $request, $handler);
        }

        // ───────────────────────────────────────────────
        // Fetch tenant from DB
        // ───────────────────────────────────────────────
        $query = $this->queryBuilder->from('tenant')->where('status', '=', 'active');

        if ($tenantUuid !== null) {
            $query->andWhere('id', '=', $tenantUuid);
        } elseif ($domain !== null) {
            $query->andWhere('domain', '=', $domain);
        }

        /** @var Tenant|false $tenant */
        $tenant = $query->fetch(Tenant::class);

        if (!$tenant) {
            return $this->notFoundResponse('Tenant not found');
        }

        // ───────────────────────────────────────────────
        // Cache tenant for subsequent requests
        // ───────────────────────────────────────────────
        $cacheKey = $tenantUuid
            ? $this->tenantKey . $tenantUuid
            : $this->tenantKey . $tenant->getDomain();
        $this->cachePool->save(
            $this->cachePool->getItem($cacheKey)
                ->set($tenant)
                ->expiresAfter($this->tenantTtl)
        );

        return $this->continue($tenant, $request, $handler);
    }

    private function extractTenantInfo(ServerRequestInterface $request): array
    {
        $tenantUuid = $request->getHeaderLine('X-Tenant') ?: null;
        $domain = $request->getUri()->getHost() ?: null;

        return [
            'uuid' => $tenantUuid,
            'domain' => $domain,
        ];
    }

    private function continue(Tenant $tenant, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->tenantService->loadTenantContext($tenant);

        return $handler->handle($request);
    }

    private function notFoundResponse(string $message): ResponseInterface
    {
        return new JsonResponse(['error' => $message], 404);
    }
}
