<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;

/**
 * HealthController is responsible for rendering the health check endpoint.
 */
final class HealthController
{
    public function __construct() {}

    /**
     * Render the home page.
     *
     * @return Response
     */
    #[Route(
        methods: 'GET',
        path: '/healthz',
        name: 'Health Check',
        summary: 'Render home page',
        tags: ['Health']
    )]
    public function healthCheck(): Response
    {
        return json(["status" => "The MonkeysLegion Tenant is healthy!"], 200)
            ->withHeader('Content-Type', 'application/json');
    }
}
