<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\MusicCatalog\Entities\Role;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Infrastructure\Persistance\Models\EloquentRoleModel;

class RoleMapper {
    public function toDomain(EloquentRoleModel $eloquentRole) {
        $id = new RoleId($eloquentRole->id);

        $role = Role::fromPersistance($id, $eloquentRole->name);
        return $role;
    }

    public function toPersistence(Role $role) {
        return [
            'id' => $role->getId()->getValue(),
            'name' => $role->getName()
        ];
    }
}