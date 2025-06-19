<?php

namespace App\Domain\MusicCatalog\Entities;

use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\ArtistImageUrl;
use JsonSerializable;

// An artist releases albums

class Artist implements JsonSerializable {
    // ArtistId = mean of distinguishing each object regardless of its form or history.
    private ArtistId $id;
    private string $name; // msut check if it's empty
    private string $bio; // dont' need a value object for just checking if value is shorter than a certain length
    private ArtistImageUrl $imageUrl;

    public function __construct(
        string $name,
        string $bio,
        ArtistImageUrl $imageUrl
    ) {
        $this->id = ArtistId::generate();
        $this->setName($name);
        $this->bio = $bio;
        $this->imageUrl = $imageUrl;
    }

    private function setName(string $name) {
        if (empty($name)) {
            throw new \DomainException('Artist name must not be empty');
        }
        $this->artistName = trim($name);
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'bio' => $this->bio,
            'image_url' => $this->imageUrl
        ];
    }
}