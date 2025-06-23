<?php

namespace App\Domain\MusicCatalog\Entities;

use JsonSerializable;
use App\Domain\MusicCatalog\ValueObjects\RoleId;
use App\Domain\MusicCatalog\ValueObjects\SongId;
use App\Domain\MusicCatalog\ValueObjects\AlbumId;
use App\Domain\MusicCatalog\ValueObjects\GenreId;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\Contribution;

class Song implements JsonSerializable {
    private SongId $id;
    private string $title;
    private GenreId $genreId;
    private ?AlbumId $albumId;
    private array $contributions = [];

    public function __construct(string $title, GenreId $genreId, ?AlbumId $albumId = null) {
        $this->id = SongId::generate();
        $this->setTitle($title);
        $this->genreId = $genreId;
        $this->albumId = $albumId;
    }

    public function setTitle(string $title) {
        if (empty($title)) {
            throw new \DomainException('Title must not be empty');
        }
        $this->title = trim($title);
    }

    public function addContributor(ArtistId $artistId, RoleId $roleId) {
        $newContributor = new Contribution($artistId, $roleId);

        // check if a given contributor already exists in the $contributions array
        foreach ($this->contributions as $existingContributor) {
            if ($existingContributor->getArtistId()->equals($newContributor->getArtistId())
                && $existingContributor->getRoleId()->equals($newContributor->getRoleId())) {
                throw new \DomainException('Artist already has this role in this song');
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

    public function getId(): SongId {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getGenreId(): GenreId {
        return $this->genreId;
    }

    public function getAlbumId(): AlbumId|null {
        return $this->albumId;
    }

    public function getContributions(): array {
        return $this->contributions;
    }

    // update methods
    public function updateTitle(string $title) {
        $this->title = $title;
    }

    public function updateGenreId(GenreId $genreId) {
        $this->genreId = $genreId;
    }

    public function updateAlbumId(AlbumId $albumId) {
        $this->albumId = $albumId;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id->getValue(),
            'title' => $this->title,
            'genre_id' => $this->genreId,
            'album_id' => $this->albumId,
            'contributions' => $this->contributions
        ];
    }

    public static function fromPersistance(
        SongId $id,
        string $title,
        GenreId $genreId,
        ?AlbumId $albumId = null,
    ): Song {
        $song = new self($title, $genreId, $albumId);
        $song->id = $id;
        return $song;
    }
}