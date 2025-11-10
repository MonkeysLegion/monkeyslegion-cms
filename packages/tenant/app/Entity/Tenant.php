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
use MonkeysLegion\Entity\Attributes\Uuid;

#[Entity]
class Tenant
{
    #[Field(type: 'uuid', autoIncrement: false, primaryKey: true)]
    #[Uuid]
    public string $id;

    #[Field(type: 'string')]
    public string $name;

    #[Field(type: 'string')]
    public string $domain;

    #[Field(type: 'enum', enumValues: ['active', 'inactive', 'suspended'], default: "active")]
    public string $status;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $createdAt;

    #[Field(type: 'datetime')]
    public \DateTimeImmutable $updatedAt;

    #[Field(type: 'json')]
    public array $settings;

    #[OneToOne(targetEntity: TenantModuleConfig::class, mappedBy: 'tenant')]
    public ?TenantModuleConfig $tenantModuleConfig = null;

    /** @var TenantUser[] */
    #[OneToMany(targetEntity: TenantUser::class, mappedBy: 'tenant')]
    public array $tenantUsers = [];

    public function __construct()
    {
        $this->tenantUsers = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    public function getTenantModuleConfig(): ?TenantModuleConfig
    {
        return $this->tenantModuleConfig;
    }

    public function setTenantModuleConfig(?TenantModuleConfig $tenantModuleConfig): self
    {
        $this->tenantModuleConfig = $tenantModuleConfig;
        return $this;
    }

    public function removeTenantModuleConfig(): self
    {
        $this->tenantModuleConfig = null;
        return $this;
    }

    public function addTenantUser(TenantUser $item): self
    {
        $this->tenantUsers[] = $item;
        return $this;
    }

    public function removeTenantUser(TenantUser $item): self
    {
        $this->tenantUsers = array_filter($this->tenantUsers, fn($i) => $i !== $item);
        return $this;
    }

    public function getTenantUsers(): array
    {
        return $this->tenantUsers;
    }
}
