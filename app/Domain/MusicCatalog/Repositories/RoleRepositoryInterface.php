<?php

namespace App\Domain\MusicCatalog\Repositories;

use App\Domain\MusicCatalog\Entities\Role;
use App\Domain\MusicCatalog\ValueObjects\RoleId;

interface RoleRepositoryInterface {
    public function create(Role $role);
    public function find(RoleId $id);
    public function index();
    public function findByName($name);
    public function delete(RoleId $id);
}