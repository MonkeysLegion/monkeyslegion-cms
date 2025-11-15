<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\TenantCore\Core\TenantContext;

final class TenantDebugController
{
    public function __construct() {}

    #[Route(
        methods: 'GET',
        path: '/_debug/tenant',
        name: 'Tenant Debug',
        summary: 'Shows currently resolved tenant',
        tags: ['Tenant']
    )]
    public function debug(): Response
    {
        $tenant = TenantContext::get();

        if (!$tenant) {
            return json(['tenant' => null], 200)
                ->withHeader('Content-Type', 'application/json');
        }

        return json([
            'id'     => $tenant->getId(),
            'domain' => $tenant->getDomain(),
            'status' => $tenant->getStatus(),
        ], 200)->withHeader('Content-Type', 'application/json');
    }
}
