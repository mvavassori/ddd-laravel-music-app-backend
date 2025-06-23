<?php

namespace App\Infrastructure\Persistance\Repositories;

use App\Domain\MusicCatalog\Entities\Role;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Infrastructure\Persistance\Mappers\RoleMapper;
use App\Infrastructure\Persistance\Models\EloquentRoleModel;
use App\Domain\MusicCatalog\Repositories\RoleRepositoryInterface;

class EloquentRoleRepoository implements RoleRepositoryInterface {
    private RoleMapper $roleMapper;
    public function __construct(RoleMapper $roleMapper) {
        $this->roleMapper = $roleMapper;
    }
    public function create(Role $role) {
        $eloquentRole = EloquentRoleModel::create($this->roleMapper->toPersistence($role));
        return $this->roleMapper->toDomain($eloquentRole);
    }

    public function index() {
        $eloquentRoles = EloquentRoleModel::all();
        $rolesArrayOfObj = [];
        foreach ($eloquentRoles as $eloquentRole) {
            $rolesArrayOfObj[] = $this->roleMapper->toDomain($eloquentRole);
        }
        return $rolesArrayOfObj;
    }

    public function find(RoleId $id) {
        $eloquentRole = EloquentRoleModel::find($id->getValue());
        if (!$eloquentRole) {
            return null;
        }
        return $this->roleMapper->toDomain($eloquentRole);
    }

    public function findByName($name) {
        $eloquentRole = EloquentRoleModel::where('name', $name)->first();
        if (!$eloquentRole) {
            return null;
        }
        return $this->roleMapper->toDomain($eloquentRole);
    }

    public function delete(RoleId $id) {
        return EloquentRoleModel::destroy($id->getValue());
    }
}