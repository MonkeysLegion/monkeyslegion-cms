<?php

declare(strict_types=1);

namespace App\Controller;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Http\Message\Response;
use MonkeysLegion\Http\Message\Stream;
use MonkeysLegion\Template\Renderer;

/**
 * HomeController is responsible for rendering the home page.
 */
final class HealthController
{
    public function __construct(private Renderer $renderer) {}

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
    public function index(): Response
    {
        return json(["status" => "The Monkey is healthy!"], 200)
            ->withHeader('Content-Type', 'application/json');
    }
}
