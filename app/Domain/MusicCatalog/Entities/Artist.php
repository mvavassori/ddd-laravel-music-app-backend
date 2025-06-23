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
        ?string $bio = null,
        ?ArtistImageUrl $imageUrl = null
    ) {
        $this->id = ArtistId::generate();
        $this->setName($name);
        $this->bio = $bio;
        $this->imageUrl = $imageUrl;
    }

    // getters
    public function getId(): ArtistId {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getBio(): string {
        return $this->bio;
    }

    public function getImageUrl(): ArtistImageUrl {
        return $this->imageUrl;
    }

    // methods used to update fields
    public function updateName(string $name): void {
        $this->setName($name); // Uses existing validation
    }
    
    public function updateBio(?string $bio): void
    {
        $this->bio = $bio;
    }
    
    public function updateImageUrl(?ArtistImageUrl $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    // validate name
    private function setName(string $name) {
        if (empty($name)) {
            throw new \DomainException('Artist name must not be empty');
        }
        $this->name = trim($name);
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'bio' => $this->bio,
            'image_url' => $this->imageUrl ? $this->imageUrl->getValue() : null
        ];
    }

    public static function fromPersistance(
        ArtistId $id,
        string $name,
        ?string $bio = null,
        ?ArtistImageUrl $imageUrl = null
    ): Artist {
        $artist = new self($name, $bio, $imageUrl);
        $artist->id = $id; // use existing id rather using the generated one on object creation
        return $artist;
    }
}