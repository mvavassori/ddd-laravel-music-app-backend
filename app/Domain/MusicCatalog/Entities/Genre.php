<?php

namespace App\Domain\MusicCatalog\Entities;

use App\Domain\MusicCatalog\ValueObjects\GenreId;

class Genre {
    private GenreId $id;
    private string $name;

    public function __construct(string $name) {
        $this->genreId = GenreId::generate();
        $this->name = $name;
    }

    public function getId(): GenreId {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    private function setName(string $name) {
        if (empty($name)) {
            throw new \DomainException('Genre name must not be empty');
        }
        $this->roleName = trim($name);
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name
        ];
    }

    public static function fromPersistance(
        GenreId $id,
        string $name
    ): Genre {
        $genre = new self($name);
        $genre->id = $id;
        return $genre;
    }
}