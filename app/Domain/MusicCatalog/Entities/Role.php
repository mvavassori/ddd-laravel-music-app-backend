<?php

namespace App\Domain\MusicCatalog\Entities;

use App\Domain\MusicCatalog\ValueObjects\RoleId;

class Role implements \JsonSerializable {
    private RoleId $id;
    private string $name;

    public function __construct(string $name) {
        $this->roleId = RoleId::generate();
        $this->setName($name);
    }

    private function setName(string $name) {
        if (empty($name)) {
            throw new \DomainException('Role name must not be empty');
        }
        $this->roleName = trim($name);
    }

    public function getId(): RoleId {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name
        ];
    }

    public static function fromPersistance(
        RoleId $id,
        string $name
    ): Role {
        $role = new self($name);
        $role->id = $id;
        return $role;
    }
}