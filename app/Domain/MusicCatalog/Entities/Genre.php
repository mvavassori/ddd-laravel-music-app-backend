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
}