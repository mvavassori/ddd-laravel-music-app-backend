<?php

namespace App\Domain\MusicCatalog\ValueObjects;

class AlbumImageUrl {
    private string $value;

    public function __construct(string $value) {
        // validate that it is actually an url
        if (!$value || !filter_var($value, FILTER_VALIDATE_URL)) {
            throw new \DomainException('Url must be valid; e.g.: https://example.com/assets/image.jpg');
        }
        $this->value = $value;
    }

    public function getValue(): string {
        return $this->value;
    }
}