<?php

namespace App\Domain\MusicCatalog\ValueObjects;

class Contribution {
    private ArtistId $artistId;
    private RoleId $roleId;

    public function __construct(ArtistId $artistId, RoleId $roleId) {
        $this->artistId = $artistId;
        $this->roleId = $roleId;
    }

    public function getArtistId(): ArtistId {
        return $this->artistId;
    }

    public function getRoleId(): RoleId {
        return $this->roleId;
    }
}