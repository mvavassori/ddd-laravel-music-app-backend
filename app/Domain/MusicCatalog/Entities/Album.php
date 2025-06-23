<?php

namespace App\Domain\MusicCatalog\Entities;

use JsonSerializable;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\Contribution;
use App\Domain\MusicCatalog\ValueObjects\AlbumImageUrl;

class Album implements JsonSerializable {
    private AlbumId $id;
    private string $title;
    private ?string $description;
    private ?AlbumImageUrl $imageUrl;
    private array $contributions = [];
    private array $songIds = [];

    public function __construct(
        string $title,
        ?string $description = null,
        ?AlbumImageUrl $imageUrl = null
    ) {
        $this->id = AlbumId::generate();
        $this->setTitle($title);
        $this->description = $description;
        $this->imageUrl = $imageUrl;
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
            if ($existingContributor->getArtistId()->equals($newContributor->getArtistId())
                && $existingContributor->getRoleId()->equals($newContributor->getRoleId())) {
                throw new \DomainException('Artist already has this role in this album');
            }
        }

        $this->contributions[] = $newContributor;
    }

    public function replaceContributors(array $newContributions): void {
        // Clear existing contributors
        $this->contributions = [];

        // Add new ones
        foreach ($newContributions as $contribution) {
            $this->addContributor($contribution['artistId'], $contribution['roleId']);
        }
    }

    public function addSong(SongId $songId) {
        if (in_array($songId, $this->songIds)) {
            throw new \DomainException('Song is already in the album');
        }
        $this->songIds[] = $songId;
    }


    public function getId(): AlbumId {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getImageUrl(): ?AlbumImageUrl {
        return $this->imageUrl;
    }

    public function getContributions(): array {
        return $this->contributions;
    }

    // update methods
    public function updateTitle(string $title) {
        $this->title = $title;
    }

    public function updateDescription(string $description) {
        $this->description = $description;
    }

    public function updateImageUrl(?AlbumImageUrl $imageUrl) {
        $this->imageUrl = $imageUrl;
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

    public static function fromPersistance(
        AlbumId $id,
        string $title,
        ?string $description = null,
        ?AlbumImageUrl $imageUrl = null
    ): Album {
        $album = new self($title, $description, $imageUrl);
        $album->id = $id; // use existing id rather using the generated one on object creation
        return $album;
    }
}