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

    public function jsonSerialize(): array {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name
        ];
    }
}