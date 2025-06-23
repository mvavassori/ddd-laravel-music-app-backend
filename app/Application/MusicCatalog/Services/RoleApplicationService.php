<?php

namespace App\Application\MusicCatalog\Services;

use App\Domain\MusicCatalog\Entities\Role;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Domain\MusicCatalog\Repositories\RoleRepositoryInterface;

class RoleApplicationService {
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository) {
        $this->roleRepository = $roleRepository;
    }

    public function findRole(string $id) {
        $roleId = new RoleId($id);
        $role = $this->roleRepository->find($roleId);
        if (!$role) {
            throw new \DomainException("Role with ID: {$id} not found");
        }
        return $this->toArray($role);
    }

    public function findAllRoles() {
        $roles = $this->roleRepository->index();
        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[] = $this->toArray($role);
        }
        return $rolesArray;
    }

    public function createRole(string $name) {
        $existingRole = $this->roleRepository->findByName($name);
        if ($existingRole) {
            throw new \DomainException("Role {$name} already exists");
        }
        $role = new Role($name);

        $this->roleRepository->create($role);

        return $this->toArray($role);
    }

    private function toArray(Role $role) {
        return [
            'id' => $role->getId()->getValue(),
            'name' => $role->getName()
        ];
    }
}