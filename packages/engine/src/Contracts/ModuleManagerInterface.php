<?php

declare(strict_types=1);

namespace MonkeysLegion\Engine\Contracts;

interface ModuleManagerInterface
{
    /**
     * Load all registered modules.
     */
    public function loadModules(): void;

    /**
     * Get a module by its name.
     */
    public function getModule(string $name): ?ModuleInterface;
}
