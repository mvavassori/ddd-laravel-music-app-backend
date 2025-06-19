<?php

namespace App\Domain\MusicCatalog\Entities;

use JsonSerializable;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\Contribution;

// every album must have at least one artist with the role that it 
class Album implements JsonSerializable {
    private AlbumId $id;
    private string $title;
    private ?string $description;
    private array $contributions = [];
    private array $songIds = [];

    public function __construct(
            string $title,
            ?string $description = null,
        ) {
            $this->id = AlbumId::generate();
            $this->setTitle($title);
            $this->description = $description;
    }

    private function setTitle(string $title) {
        if (empty($title)) {
            throw new \DomainException('Album title must not be empty');
        }
        $this->title = trim($title);
    }

    public function addContributor(ArtistId $artistId, RoleId $roleId) {
        $newContributor = new Contribution($artistId, $roleId);

        // check if a given contributor already exists in the $contributions array
        foreach ($this->contributions as $existingContributor) {
            if ($existingContributor->getArtistId()->getValue() === $newContributor->getArtistId()->getValue()
                && $existingContributor->getRoleId()->getValue() === $newContributor->getRoleId()->getValue()) {
                throw new \DomainException('Artist already has this role in this album');
            }
        }

        $this->contributions[] = $newContributor;
    }

    public function addSong(SongId $songId) {
        if(in_array($songId, $this->songIds)) {
            throw new \DomainException('Song is already in the album');
        }
        $this->songIds[] = $songId;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id->getValue(),
            'title' => $this->title,
            'description' => $this->description,
            'song_ids' => $this->songIds,
            'contributions' => $this->contributions
        ];
    }
}