<?php

declare(strict_types=1);

namespace App\Entity;

use MonkeysLegion\Entity\Attributes\Entity;
use MonkeysLegion\Entity\Attributes\Field;
use MonkeysLegion\Entity\Attributes\OneToOne;
use MonkeysLegion\Entity\Attributes\OneToMany;
use MonkeysLegion\Entity\Attributes\ManyToOne;
use MonkeysLegion\Entity\Attributes\ManyToMany;
use MonkeysLegion\Entity\Attributes\JoinTable;

#[Entity]
class TenantModuleConfig
{
    #[Field(type: 'uuid')]
    public string $id;

    #[Field(type: 'string')]
    public string $moduleKey;

    #[Field(type: 'string')]
    public string $version;

    #[Field(type: 'boolean')]
    public bool $enabled;

    #[Field(type: 'json')]
    public array $config;

    #[OneToOne(targetEntity: Tenant::class, inversedBy: 'tenantModuleConfig')]
    public Tenant $tenant;

    public function __construct() {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getModuleKey(): string
    {
        return $this->moduleKey;
    }

    public function setModuleKey(string $moduleKey): self
    {
        $this->moduleKey = $moduleKey;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }
}
