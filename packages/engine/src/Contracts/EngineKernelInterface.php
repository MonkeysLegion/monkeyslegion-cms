<?php

declare(strict_types=1);

namespace MonkeysLegion\Engine\Contracts;

use Psr\Container\ContainerInterface;

interface EngineKernelInterface
{
    /**
     * Boot the engine kernel.
     */
    public function boot(): void;

    /**
     * Register a module with the engine kernel.
     */
    public function registerModule(ModuleInterface $module): void;

    /**
     * Get the service container.
     */
    public function getContainer(): ContainerInterface;
}
