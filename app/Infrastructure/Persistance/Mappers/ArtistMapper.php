<?php

namespace App\Infrastructure\Persistance\Mappers;

use App\Domain\MusicCatalog\Entities\Artist;
use App\Domain\MusicCatalog\ValueObjects\ArtistId;
use App\Domain\MusicCatalog\ValueObjects\ArtistImageUrl;
use App\Infrastructure\Persistance\Models\EloquentArtistModel;

class ArtistMapper {
    public function toDomain(EloquentArtistModel $eloquentArtist): Artist {
        $imageUrl = null;
        if ($eloquentArtist->image_url) {
            $imageUrl = new ArtistImageUrl($eloquentArtist->image_url);
        }

        $id = new ArtistId($eloquentArtist->id);

        $artist = Artist::fromPersistance(
            $id,
            $eloquentArtist->name,
            $eloquentArtist->bio,
            $imageUrl
        );
        return $artist;
    }

    public function toPersistence(Artist $artist) {
        return [
            'id' => $artist->getId()->getValue(),
            'name' => $artist->getName(),
            'bio' => $artist->getBio(),
            'image_url' => $artist->getImageUrl()?->getValue(),
        ];
    }
}