<?php

namespace App\Domain\MusicCatalog\ValueObjects;

use App\Domain\MusicCatalog\Entities\Role;
use App\Domain\MusicCatalog\Entities\Artist;

class Contribution {
    private ArtistId $artistId;
    private RoleId $roleId;

    public function __construct(ArtistId $artistId, RoleId $roleId) {
        $this->artistId = $artistId;
        $this->roleId = $roleId;
    }

    public function getArtistId() {
        return $this->artistId;
    }

    public function getRoleId() {
        return $this->roleId;
    }
}