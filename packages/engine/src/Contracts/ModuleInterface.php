<?php

declare(strict_types=1);

namespace MonkeysLegion\Engine\Contracts;

use Psr\Container\ContainerInterface;

interface ModuleInterface
{
    /**
     * Register the module's services with the given container.
     */
    public function register(ContainerInterface $container): void;

    /**
     * Boot the module.
     */
    public function boot(): void;
}
